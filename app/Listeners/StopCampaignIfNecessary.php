<?php

namespace App\Listeners;

use App\Events\WorkerFinished;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\CampaignStopped;

class StopCampaignIfNecessary
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WorkerFinished  $event
     * @return void
     */
    public function handle(WorkerFinished $event)
    {
        $worker = $event->worker;
        // Stop the current worker
        $worker->stop();
        // Grab the current campaign
        $campaign = $worker->campaign;
        // Check if campaign should be stopped
        if ($campaign->shouldStop()) {
            // event(new CampaignStopped($campaign));
            $campaign->stop();
        }
    }
}
