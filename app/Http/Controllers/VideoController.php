<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;
use App\Services\ValidatesTweet;

class VideoController extends Controller
{
    use ValidatesTweet;

    /**
     * Build a simulating tweet to see if it can be posted lately
     *
     * @return array
     */
    private function simulateTweet()
    {
        return [
            'message' => request('title'),
            'link'    => 'https://www.youtube.com/watch?v=' . request('slug'),
        ];
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::paginate(Video::PAGINATION);
        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate if tweet is valid (custom validation specific to Twitter regarding the 140 chacracters limit)
        if ( ! $this->tweetIsValid($this->simulateTweet())) {
            return back()->withInput()->with('danger', 'Tweet is invalid! More than 140 characters provided!');
        }
        // If validation passes, generate the video
        Video::create($request->all());
        return redirect()->route('videos.index')->with('success', 'Video created succesfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        // Validate if tweet is valid (custom validation specific to Twitter regarding the 140 chacracters limit)
        if ( ! $this->tweetIsValid($this->simulateTweet())) {
            return back()->withInput()->with('danger', 'Tweet is invalid! More than 140 characters provided!');
        }
        // If validation passes, generate the video
        $video->update($request->all());
        return redirect()->route('videos.index')->with('success', 'Video updated succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video deleted succesfully!');
    }
}
