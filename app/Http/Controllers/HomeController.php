<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website;
use App\Video;
use Abraham\TwitterOAuth\TwitterOAuth;
use Redis;

class HomeController extends Controller
{
    /**
     * Show the "Coming Soon" homepage.
     *
     * @return \Illuminate\Http\Response|Redirect
     */
    public function index()
    {
	    if (request()->url() === env('ADMIN_URL')) {
	    	return view('welcome');
		}else{
			return redirect()->route('watch');
		}
    }

    /**
     * Display a listing of all videos belonging to the current website.
     *
     * @return \Illuminate\Http\Response
     */
    public function watch()
    {
		if (request()->url() === env('ADMIN_URL').'/watch') {
	    	abort(404);
		}

        $website = Website::findByUrl(request()->url());

        $videos = Video::paginate(Video::PAGINATION);

        return view('watch', compact('website', 'videos'));
    }

    /**
     * Redirect the user to Twitter to obtain its token.
     *
     * @param App\Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showVideo(Video $video)
    {
        $website = Website::findByUrl(request()->url());

        $connection = new TwitterOAuth($website->app_key, $website->app_secret);

        $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => route('video.callback', $video->id)]);

        Redis::set('oauth_token', $request_token['oauth_token']);
        Redis::set('oauth_token_secret', $request_token['oauth_token_secret']);

        $oauth_url = $connection->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);

        return redirect($oauth_url);
    }

    /**
     * Redirect the user to Youtube after obtaining its token.
     *
     * @param App\Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callbackVideo(Video $video)
    {
        $website = Website::findByUrl(request()->url());

        $request_token = [];
        $request_token['oauth_token'] = Redis::get('oauth_token');
        $request_token['oauth_token_secret'] = Redis::get('oauth_token_secret');

        if (null !== request('oauth_token') && $request_token['oauth_token'] !== request('oauth_token')) {
            // Abort! Something is wrong.
            dd('Wrong mother fucker!');
        }

        $connection = new TwitterOAuth($website->app_key, $website->app_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);

        $user_data = $connection->oauth('oauth/access_token', ['oauth_verifier' => request('oauth_verifier')]);

        dd($user_data, $video->id);
    }
}
