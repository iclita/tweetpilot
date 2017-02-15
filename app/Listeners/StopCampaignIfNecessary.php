<?php

namespace App\Listeners;

use App\Events\WorkerFinished;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            $campaign->stop();
        }
    }
}
