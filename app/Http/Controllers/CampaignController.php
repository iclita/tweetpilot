<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Website;
use Illuminate\Http\Request;
use App\Services\ValidatesTweet;
use DB;

class CampaignController extends Controller
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
            'message' => request('custom_message'),
            'link'    => request('custom_link'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = Campaign::all();
        // If we have no videos, campaigns will not run without custom data
        $hasVideos = DB::table('videos')->count() > 0;
        // If we have no links, campaigns will not run without custom data
        $hasLinks = DB::table('links')->count() > 0;
        return view('campaigns.index', compact('campaigns', 'hasVideos', 'hasLinks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $websites = Website::getAvailable();
        // Check if we still have websites available
        if (count($websites) > 0) {
            return view('campaigns.create', compact('websites'));
        }
        return back()->with('warning', 'You have no more websites available!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'custom_message' => 'required_with:custom_link',
            'custom_link' => 'required_with:custom_message',
            'post_id' => 'required_if:type,like,retweet',
        ]);
        // Validate if tweet is valid (custom validation specific to Twitter regarding the 140 chacracters limit)
        if ( ! $this->tweetIsValid($this->simulateTweet())) {
            return back()->withInput()->with('danger', 'Tweet is invalid! More than 140 characters provided!');
        }
        // If validation passes then we move forward
        Campaign::make($request->all());
        return redirect()->route('campaigns.index')->with('success', 'Campaign created succesfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        $this->validate($request, [
            'custom_message' => 'required_with:custom_link',
            'custom_link' => 'required_with:custom_message',
            'post_id' => 'required_if:type,like,retweet',
        ]);
        // Validate if tweet is valid (custom validation specific to Twitter regarding the 140 chacracters limit)
        if ( ! $this->tweetIsValid($this->simulateTweet())) {
            return back()->withInput()->with('danger', 'Tweet is invalid! More than 140 characters provided!');
        }
        // If validation passes then we move forward
        $campaign->update($request->all());
        return redirect()->route('campaigns.index')->with('success', 'Campaign updated succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted succesfully!');
    }

    /**
     * Toggle campaign active state.
     *
     * @param  \App\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Campaign $campaign)
    {
        $campaign->toggleActive();
        return back();
    }

    /**
     * Start campaign.
     *
     * @param  \App\Campaign  $campaign
     * @return array|json
     */
    public function start(Campaign $campaign)
    {
        $campaign->start();
        return ['id' => $campaign->id];
    }

    /**
     * Stop campaign.
     *
     * @param  \App\Campaign  $campaign
     * @return array|json
     */
    public function stop(Campaign $campaign)
    {
        $campaign->stop();
        return ['id' => $campaign->id];
    }

    /**
     * Pause campaign.
     *
     * @param  \App\Campaign  $campaign
     * @return array|json
     */
    public function pause(Campaign $campaign)
    {
        $campaign->pause();
        return ['id' => $campaign->id];
    }

    /**
     * Resume campaign.
     *
     * @param  \App\Campaign  $campaign
     * @return array|json
     */
    public function resume(Campaign $campaign)
    {
        $campaign->resume();
        return ['id' => $campaign->id];
    }
}
