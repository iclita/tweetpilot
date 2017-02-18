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

Route::get('test', function(){

	$campaign = \App\Campaign::first();
	event(new \App\Events\CampaignStarted($campaign));

});

// Login Redirect Route...
Route::get('login', function() {
	return redirect('/');
});
// Home Routes...
Route::get('/', 'HomeController@index')->name('index');
Route::get('watch', 'HomeController@watch')->name('watch');
Route::get('video/{video}/preview', 'HomeController@previewVideo')->name('video.preview');
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
		Route::post('debug-post', 'AdminController@debugPost')->name('debug.post');
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
		Route::get('campaigns/{campaign}/stop', 'CampaignController@stop')->name('campaigns.stop');
		Route::get('campaigns/{campaign}/pause', 'CampaignController@pause')->name('campaigns.pause');
		Route::get('campaigns/{campaign}/resume', 'CampaignController@resume')->name('campaigns.resume');
		// Worker Routes...
		Route::get('campaigns/{campaign}/workers', 'WorkerController@index')->name('workers.index');
		Route::get('campaigns/{campaign}/workers/add', 'WorkerController@add')->name('workers.add');
		Route::delete('workers/{worker}/delete', 'WorkerController@delete')->name('workers.delete');
		Route::get('workers/{worker}/toggle-synced', 'WorkerController@toggleSynced')->name('workers.toggle.synced');
	});
});