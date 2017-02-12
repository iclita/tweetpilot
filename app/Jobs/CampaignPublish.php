<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Worker;
use App\Post;
use Illuminate\Support\Collection;
use Abraham\TwitterOAuth\TwitterOAuth;
use DB;

class CampaignPublish implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The worker processing this job.
     *
     * @var App\Worker
     */
    protected $worker;

    /**
     * The tokens processed by this job.
     *
     * @var Illuminate\Support\Collection
     */
    protected $tokens;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Worker $worker, Collection $tokens)
    {
        $this->worker = $worker;
        $this->tokens = $tokens;
    }

    private function sendPostRequests()
    {
        // Get current campaign and other relevant data
        $campaign = $this->worker->campaign;
        $app_key = $campaign->website->app_key;
        $app_secret = $campaign->website->app_secret;
        $message = $campaign->custom_message . ' ' . $campaign->custom_link;

        foreach ($this->tokens as $token) {
            // Stop the processing if the campaign has been stopped by the admin
            if ($campaign->isStopped()) {
                return;
            }
            // Process the token only if it has not been processed before
            if ($this->worker->processedToken($token)) {
                continue;
            }
            // Grab a Twitter connection
            $connection = new TwitterOAuth($app_key, $app_secret, $token->access_token, $token->access_token_secret);
            try {
                $statuses = $connection->post('statuses/update', ['status' => $message]);
                // Check for errors
                if ($connection->getLastHttpCode() == 200) {
                    // Tweet posted succesfully
                    $data = ['post_id' => $statuses->id];
                    Post::make($data, $token);
                } else {
                    // Handle error case
                    $error_data = ['type' => 'post', 'message' => 'Post Error!'];
                    DB::table('errors')->insert($error_data);
                    // $token->invalidateIfNecessary($e->getMessage());
                }
            } catch(\Exception $e) {
                $error_data = ['type' => 'post', 'message' => $e->getMessage()];
                DB::table('errors')->insert($error_data);
                // $token->invalidateIfNecessary($e->getMessage());
            }
            // Pause the processing if the campaign has been paused by the admin
            if ($campaign->isPaused()) {
                $this->worker->setResumeToken($token);
                return;
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $campaign = $this->worker->campaign;

        if ($campaign->isPost()) {
            $this->sendPostRequests();
        } elseif ($campaign->isLike()) {
            // $this->sendLikeRequests();
        } elseif ($this->isRetweet()) {
            // $this->sendRetweetRequests();
        } else {
            throw new \Exception('Unknown campaign type!');
        }

        $campaign->stop();
    }
}
