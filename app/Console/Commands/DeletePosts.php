<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DeletePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amapilot:delete-posts {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all posts older than a week';

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
        DB::table('posts')->whereRaw('TIMESTAMPDIFF(WEEK, created_at, NOW()) > 0')->delete();
    }
}
