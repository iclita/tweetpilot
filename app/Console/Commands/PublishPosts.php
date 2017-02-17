<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CampaignsManager as Manager;
use App\Campaign;

class PublishPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amapilot:publish-posts {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically publishes posts on all campaigns';

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
        // Check if we should start the campaigns
        if (Manager::shouldRun()) {
            // Start all active campaigns
            Manager::run();
        }
    }
}
