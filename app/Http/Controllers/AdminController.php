<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Services\Settings;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
    	$total_tokens = DB::table('tokens')->count();

        $today = (new Carbon('today'))->toDateTimeString();
        $tomorrow = (new Carbon('tomorrow'))->toDateTimeString();
        $today_tokens = DB::table('tokens')->whereRaw("valid = 1 AND (created_at BETWEEN '{$today}' AND '{$tomorrow}')")->count();

        $publish_intervals = [
            '24' => '1 (24h)',
            '12' => '2 (12h)',
            '8'  => '3 (8h)',
            '6'  => '4 (6h)',
            '4'  => '6 (4h)',
            '3'  => '8 (3h)',
            '2'  => '12 (2h)',
        ];

        $growth_percentages = [
            '0' => '0%',
            '10' => '10%',
            '20' => '20%',
            '30' => '30%',
            '40' => '40%',
            '50' => '50%',
            '60' => '60%',
            '70' => '70%',
            '80' => '80%',
            '90' => '90%',
            '100' => '100%',
        ];      

        return view('home', compact('total_tokens', 'today_tokens', 'publish_intervals', 'growth_percentages'));
    }

    /**
     * Show the application dashboard.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeSettings(Request $request)
    {
    	Settings::set('is_auto', $request->input('is_auto'));
    	Settings::set('publish_interval', $request->input('publish_interval'));
    	Settings::set('growth_percentage', $request->input('growth_percentage'));
    	Settings::set('num_workers', $request->input('num_workers'));

    	return back()->with('success', 'Settings changed succesfully');
    }

    /**
     * Check if a post exists.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function debugPost(Request $request)
    {
        $post_id = $request->input('post_id');
        $url = "https://twitter.com/statuses/{$post_id}";
        // Navigate to Twitter to see if post still exists
        return redirect()->away($url);
    }
}
