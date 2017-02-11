<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Website;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = Campaign::all();
        return view('campaigns.index', compact('campaigns'));
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
    public function toggle(Campaign $campaign)
    {
        $campaign->toggleState();
        return back();
    }
}
