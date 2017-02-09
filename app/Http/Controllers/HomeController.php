<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website;
use App\Video;

class HomeController extends Controller
{
    /**
     * Show the "Coming Soon" homepage.
     *
     * @return \Illuminate\Http\Response|Redirect
     */
    public function index()
    {
        dd('here');
	    if (request()->url() === env('APP_URL')) {
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
		if (request()->url() === env('APP_URL').'/watch') {
	    	abort(404);
		}

        $website = Website::findByUrl(request()->url());

        $videos = Video::paginate(Video::PAGINATION);

        return view('watch', compact('website', 'videos'));
    }
}
