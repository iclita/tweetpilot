<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Campaign;
use App\Events\CampaignStopped;

class StopCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amapilot:stop-campaigns {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks if any of the available campaigns needs to be stopped';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Campaign::chunk(10, function($campaigns) {
            foreach ($campaigns as $campaign) {
                // Check if campaign should be stopped
                if ($campaign->shouldStop()) {
                    event(new CampaignStopped($campaign));
                    $campaign->stop();
                }
            }
        });
    }
}
