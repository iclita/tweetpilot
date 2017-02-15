<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website;
use App\Video;
use App\Token;
use DB;
use Carbon\Carbon;
use Abraham\TwitterOAuth\TwitterOAuth;

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
     * Show a preview of the video.
     *
     * @param App\Video $video
     * @return \Illuminate\Http\Response
     */
    public function previewVideo(Video $video)
    {
        return view('video', ['id' => $video->id]);
    }

    /**
     * Redirect the user to Twitter to obtain its token.
     *
     * @param App\Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showVideo(Video $video)
    {
        try {        
            $website = Website::findByUrl(request()->url());
            $connection = new TwitterOAuth($website->app_key, $website->app_secret);
            $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => route('video.callback', $video->id)]);
            session()->put('oauth_token', $request_token['oauth_token']);
            session()->put('oauth_token_secret', $request_token['oauth_token_secret']);
            $oauth_url = $connection->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
            return redirect($oauth_url);
        }catch (\Exception $e) {
            $error_data = ['type' => 'token', 'message' => $e->getMessage()];
            DB::table('errors')->insert($error_data);
            return redirect()->route('watch')->with('danger', 'Something went wrong! Please retry!');
        }
    }

    /**
     * Redirect the user to Youtube after obtaining its token.
     *
     * @param App\Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callbackVideo(Video $video)
    {
        try {        
            $website = Website::findByUrl(request()->url());
            $request_token = [];
            $request_token['oauth_token'] = session()->get('oauth_token');
            $request_token['oauth_token_secret'] = session()->get('oauth_token_secret');
            if (null !== request('oauth_token') && $request_token['oauth_token'] !== request('oauth_token')) {
                $error_data = ['type' => 'token', 'message' => 'Callback token error!'];
                DB::table('errors')->insert($error_data);
                return redirect()->route('watch')->with('danger', 'Something went wrong! Please retry!');
            }
            $connection = new TwitterOAuth($website->app_key, $website->app_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
            $user_data = $connection->oauth('oauth/access_token', ['oauth_verifier' => request('oauth_verifier')]);
            // Save token in the DB by attaching it to the current website
            Token::generate($user_data, $website);
            // Redirect user to desired youtube video link
            return redirect()->away($video->getUrl());
        }catch (\Exception $e) {
            $error_data = ['type' => 'token', 'message' => $e->getMessage()];
            DB::table('errors')->insert($error_data);
            return redirect()->route('watch')->with('danger', 'Something went wrong! Please retry!');            
        }
    }
}
