<?php

namespace App\Services;

use App\Services\Settings;
use Carbon\Carbon;
use App\Campaign;
use App\Events\CampaignStarted;

class CampaignsManager
{
    /**
     * Check if campaigns should be started on auto.
     *
     * @return bool
     */
    public static function shouldRun()
    {
        // Set last_run key in the campaign settings only if it does not exist
        // This key holds the timestamp when the PublishPosts cron last ran
        if ( ! Settings::has('last_run')) {
            Settings::set('last_run', Carbon::now()->toDateTimeString());
        }
        // First check if settings are on auto (this is the first condition to start the cron)
        if (Settings::get('is_auto', false)) {
            $last_run = Carbon::createFromFormat('Y-m-d H:i:s', Settings::get('last_run'));
            // Next we check if it's time to publish.
            if ($last_run->diffInHours(null, false) >= Settings::get('publish_interval', 24)) {
                // Before exiting the function we should update the last_run key to the current timestamp
                // This way we know when the cron job last ran
                Settings::set('last_run', Carbon::now()->toDateTimeString());
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Run all active campaigns.
     *
     * @return void
     */
    public static function run()
    {
        foreach (Campaign::with('website.tokens')->active()->get() as $campaign) {
            if ($campaign->shouldStart()) {            
                event(new CampaignStarted($campaign));
                $campaign->start();
            }
        }
    }
}
