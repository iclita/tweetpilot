<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test/{id}', function($id){

		$campaign = \App\Campaign::find($id);
        // Get total number of valid tokens
        $num_tokens = $campaign->website->getValidTokensCount();
        // Get all the workers that have been synced with Forge
        $workers = $campaign->workers()->synced()->get();
        // Get number of workers
        $num_workers = $workers->count();
        // Abort if no workers detected for this campaign
        if ($num_workers === 0) {
            throw new \Exception("Campaign {$campaign->id} has no workers!");
        }
        // Calculate how much load every worker should cary
        $worker_load = ceil($num_tokens/$workers->count());
        // Distribute the tasks uniformly based on worker load
        for ($i=0; $i<$num_workers; $i++) {
            $offset = $i * $worker_load;
            $tokens = $campaign->website->tokens()->valid()
                                              ->skip($offset)
                                              ->take($worker_load)
                                              ->get();
            $worker = $workers[$i];

			// Get current campaign and other relevant data
	        $campaign = $worker->campaign;
	        $app_key = $campaign->website->app_key;
	        $app_secret = $campaign->website->app_secret;
	        $message = $campaign->custom_message . ' ' . $campaign->custom_link;

	        foreach ($tokens as $token) {
	            // Stop the processing if the campaign has been stopped by the admin
	            // if ($campaign->isStopped()) {
	            //     return;
	            // }
	            // Process the token only if it has not been processed before
	            if ($worker->processedToken($token)) {
	                continue;
	            }
	            // Grab a Twitter connection
	            $connection = new \Abraham\TwitterOAuth\TwitterOAuth($app_key, $app_secret, $token->access_token, $token->access_token_secret);
	            try {
	                $statuses = $connection->post('statuses/update', ['status' => $message]);
	                // Check for errors
	                if ($connection->getLastHttpCode() == 200) {
	                    // Tweet posted succesfully
	                    $data = ['post_id' => $statuses->id];
	                    Post::make($data, $token);
	                } else {
	                    // Handle error case
	                    $error_data = ['type' => 'post', 'message' => 'Post Error!'];
	                    DB::table('errors')->insert($error_data);
	                    // $token->invalidateIfNecessary($e->getMessage());
	                }
	            } catch(\Exception $e) {
	                $error_data = ['type' => 'post', 'message' => $e->getMessage()];
	                DB::table('errors')->insert($error_data);
	                // $token->invalidateIfNecessary($e->getMessage());
	            }
	            // Pause the processing if the campaign has been paused by the admin
	            if ($campaign->isPaused()) {
	                $worker->setResumeToken($token);
	                return;
	            }
	        }
      
        }

});

// Login Redirect Route...
Route::get('login', function() {
	return redirect('/');
});
// Home Routes...
Route::get('/', 'HomeController@index')->name('index');
Route::get('watch', 'HomeController@watch')->name('watch');
Route::get('video/{video}/show', 'HomeController@showVideo')->name('video.show');
Route::get('video/{video}/callback', 'HomeController@callbackVideo')->name('video.callback');
// Secret Routes...
Route::group(['prefix' => env('ADMIN_ROUTE')], function() {
	// Authentication Routes...
	Route::get('login', 'LoginController@showLoginForm')->name('login');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout')->name('logout');
	// Admin Routes...
	Route::group(['middleware' => 'auth'], function() {
		// Dashboard Routes...
		Route::get('home', 'AdminController@home')->name('home');
		Route::post('change-settings', 'AdminController@changeSettings')->name('change.settings');
		// Website Routes...
		Route::resource('websites', 'WebsiteController');
		// Video Routes...
		Route::resource('videos', 'VideoController');
		// Affiliate Link Routes...
		Route::resource('links', 'LinkController');
		// Campaign Routes...
		Route::resource('campaigns', 'CampaignController');
		Route::get('campaigns/{campaign}/toggle-active', 'CampaignController@toggleActive')->name('campaigns.toggle.active');
		Route::get('campaigns/{campaign}/start', 'CampaignController@start')->name('campaigns.start');
		// Worker Routes...
		Route::get('campaigns/{campaign}/workers', 'WorkerController@index')->name('workers.index');
		Route::get('campaigns/{campaign}/workers/add', 'WorkerController@add')->name('workers.add');
		Route::delete('workers/{worker}/delete', 'WorkerController@delete')->name('workers.delete');
		Route::get('workers/{worker}/toggle-synced', 'WorkerController@toggleSynced')->name('workers.toggle.synced');
	});
});