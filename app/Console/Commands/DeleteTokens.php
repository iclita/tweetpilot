<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DeleteTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amapilot:delete-tokens {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all invalid tokens';

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
        DB::table('tokens')->where('valid', false)
                           ->delete();
    }
}
