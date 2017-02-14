<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Http\Request;
use App\Services\ValidatesTweet;

class LinkController extends Controller
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
            'message' => request('description'),
            'link'    => request('url'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = Link::paginate(Link::PAGINATION);
        return view('links.index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('links.create');
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
        Link::create($request->all());
        return redirect()->route('links.index')->with('success', 'Link created succesfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function edit(Link $link)
    {
        return view('links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Link $link)
    {
        // Validate if tweet is valid (custom validation specific to Twitter regarding the 140 chacracters limit)
        if ( ! $this->tweetIsValid($this->simulateTweet())) {
            return back()->withInput()->with('danger', 'Tweet is invalid! More than 140 characters provided!');
        }
        // If validation passes, generate the video
        $link->update($request->all());
        return redirect()->route('links.index')->with('success', 'Link updated succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        $link->delete();
        return redirect()->route('links.index')->with('success', 'Link deleted succesfully!');
    }
}
