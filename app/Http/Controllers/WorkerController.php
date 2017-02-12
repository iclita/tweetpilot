<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Campaign;
use App\Worker;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function index(Campaign $campaign)
    {
        $workers = $campaign->workers;
        return view('workers.index', compact('campaign', 'workers'));
    }

    /**
     * Add a new resource.
     *
     * @param App\Campaign $campaign
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Campaign $campaign)
    {
    	$worker = new Worker();
    	$campaign->workers()->save($worker);
    	return back()->with('success', 'Worker added sucessfully!');
    }

    /**
     * Delete an existing resource resource.
     *
     * @param App\Worker $worker
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Worker $worker)
    {
    	$worker->delete();
    	return back()->with('success', 'Worker deleted sucessfully!');
    }

    /**
     * Toggle worker synced state.
     *
     * @param  \App\worker  $worker
     * @return \Illuminate\Http\Response
     */
    public function toggleSynced(Worker $worker)
    {
        $worker->toggleSynced();
        return back();
    }
}
