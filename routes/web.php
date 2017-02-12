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
		// Website Routes...
		Route::resource('websites', 'WebsiteController');
		// Video Routes...
		Route::resource('videos', 'VideoController');
		// Affiliate Link Routes...
		Route::resource('links', 'LinkController');
		// Campaign Routes...
		Route::resource('campaigns', 'CampaignController');
		Route::get('campaigns/{campaign}/toggle', 'CampaignController@toggle')->name('campaigns.toggle');
	});
});