<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Worker;
use App\Post;
use App\Video;
use App\Link;
use App\Campaign;
use Illuminate\Support\Collection;
use Abraham\TwitterOAuth\TwitterOAuth;
use DB;
use App\Events\WorkerFinished;
use Carbon\Carbon;

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

    /**
     * Get custom data to be posted associated with a given campaign.
     *
     * @param App\Campaign $campaign
     * @return string
     */
    private function getCustomPost(Campaign $campaign)
    {
        return $campaign->custom_message . ' ' . $campaign->custom_link;
    }

    /**
     * Get video (growth) data to posted.
     *
     * @return string
     */
    private function getVideoPost()
    {
        // This means we should publish a growth (video) link
        $video = Video::oldest('updated_at')->first();
        // Abort if no video was found
        if (is_null($video)) {
            throw new \Exception('You have no videos!');
        }
        // Move the video to the end of the stack
        // This is done because we want to publish videos by rotation
        $video->update(['updated_at' => Carbon::now()]);        
        // Compose the data to be posted
        return $video->title . ' ' . route('video.preview', ['id' => $video->id]);
    }

    /**
     * Get link (money) data to posted.
     *
     * @return string
     */
    private function getLinkPost()
    {
        // This means we should publish a money (affiliate) link
        $link = Link::oldest('updated_at')->first();
        // Abort if no link was found
        if (is_null($link)) {
            throw new \Exception('You have no links!');
        }
        // Move the link to the end of the stack
        // This is done because we want to publish links by rotation
        $link->update(['updated_at' => Carbon::now()]);        
        // Compose the data to be posted
        return $link->description . ' ' . $link->url;
    }

    /**
     * Get data to be posted associated with a given campaign on a given iteration.
     *
     * @param string $index
     * @param string $step
     * @param App\Campaign $campaign
     * @return string
     */
    private function getPostData($index, $step, Campaign $campaign)
    {
        // If we have custom data then that is what should be posted
        if ($campaign->isCustom()) {
            return $this->getCustomPost($campaign);
        } else {
            // If we have $step=0 it means we have only link posts
            if ($step === 0) {
                return $this->getLinkPost();
            }
            // If we arrived here it means we have both types of posts (video and link)
            if ($index % $step === 0) {
                return $this->getVideoPost();
            } else {
                return $this->getLinkPost();
            }
        }
    }

    /**
     * Send post (tweet) requests to all tokens (users).
     *
     * @return void
     */
    private function sendPostRequests()
    {
        // Get growth percentage from settings
        $growth_percentage = settings('growth_percentage', 0);
        // Calculate the number of posts that should be for growth (video links not money)
        $num_growth = round(($growth_percentage * $this->tokens->count())/100);
        // Get the step: To understand the step we need to give the following example:
        // Say we have 100 tokens to process with growth_percentage of 20%
        // This means num_growth will be 20*100/100=20
        // Now if we do 100/20=5 we get the step
        // The step of 5 means once every 5 iterations we must publish a video link
        // The rest are money (affiliate) links
        if ($num_growth > 0) {
            $step = round($this->tokens->count()/$num_growth); 
        } else {
            $step = 0;
        }
        // Get current campaign and other relevant data
        $campaign = $this->worker->campaign;
        $app_key = $campaign->website->app_key;
        $app_secret = $campaign->website->app_secret;

        foreach ($this->tokens as $index => $token) {
            // Stop the processing if the campaign has been stopped by the admin
            if ($campaign->isStopped()) {
                return;
            }
            // Process the token only if it has not been processed before after campaign has been resumed
            if ($this->worker->processedToken($token)) {
                continue;
            }
            try {
                // Grab a Twitter connection
                $connection = new TwitterOAuth($app_key, $app_secret, $token->access_token, $token->access_token_secret);
                // Get the message to post
                $message = $this->getPostData($index, $step, $campaign);
                // Post on Twitter
                $statuses = $connection->post('statuses/update', ['status' => $message]);
                // Check for errors
                if ($connection->getLastHttpCode() == 200) {
                    // Tweet posted succesfully
                    $data = ['post_id' => $statuses->id];
                    Post::make($data, $token);
                } else {
                    // Handle error case
                    $error_data = ['type' => 'post', 'message' => "Post Error! Token: {$token->user_id}"];
                    DB::table('errors')->insert($error_data);
                    // Token is not valid anymore :( 
                    $token->invalidate();
                }
            } catch(\Exception $e) {
                $error_data = ['type' => 'post', 'message' => $e->getMessage()];
                DB::table('errors')->insert($error_data);
            }
            // Pause the processing if the campaign has been paused by the admin
            if ($campaign->isPaused()) {
                $this->worker->setResumeToken($token);
                return;
            }
        }
    }

    /**
     * Send like requests to all tokens (users).
     *
     * @return void
     */
    private function sendLikeRequests()
    {
        // Get current campaign and other relevant data
        $campaign = $this->worker->campaign;
        $app_key = $campaign->website->app_key;
        $app_secret = $campaign->website->app_secret;

        foreach ($this->tokens as $token) {
            // Stop the processing if the campaign has been stopped by the admin
            if ($campaign->isStopped()) {
                return;
            }
            // Process the token only if it has not been processed before after campaign has been resumed
            if ($this->worker->processedToken($token)) {
                continue;
            }
            try {
                // Grab a Twitter connection
                $connection = new TwitterOAuth($app_key, $app_secret, $token->access_token, $token->access_token_secret);
                // Like on Twitter
                $statuses = $connection->post('favorites/create', ['id' => $campaign->post_id]);
                // Check for errors
                if ( ! $connection->getLastHttpCode() == 200) {
                    // Handle error case
                    $error_data = ['type' => 'like', 'message' => "Like Error! Token: {$token->user_id}"];
                    DB::table('errors')->insert($error_data);
                    // Token is not valid anymore :( 
                    $token->invalidate();
                }
            } catch(\Exception $e) {
                $error_data = ['type' => 'like', 'message' => $e->getMessage()];
                DB::table('errors')->insert($error_data);
            }
            // Pause the processing if the campaign has been paused by the admin
            if ($campaign->isPaused()) {
                $this->worker->setResumeToken($token);
                return;
            }
        }
    }

    /**
     * Send retweet requests to all tokens (users).
     *
     * @return void
     */
    private function sendRetweetRequests()
    {
        // Get current campaign and other relevant data
        $campaign = $this->worker->campaign;
        $app_key = $campaign->website->app_key;
        $app_secret = $campaign->website->app_secret;

        foreach ($this->tokens as $token) {
            // Stop the processing if the campaign has been stopped by the admin
            if ($campaign->isStopped()) {
                return;
            }
            // Process the token only if it has not been processed before after campaign has been resumed
            if ($this->worker->processedToken($token)) {
                continue;
            }
            try {
                // Grab a Twitter connection
                $connection = new TwitterOAuth($app_key, $app_secret, $token->access_token, $token->access_token_secret);
                // Retweet on Twitter
                $statuses = $connection->post('statuses/retweet', ['id' => $campaign->post_id]);
                // Check for errors
                if ( ! $connection->getLastHttpCode() == 200) {
                    // Handle error case
                    $error_data = ['type' => 'retweet', 'message' => "Retweet Error! Token: {$token->user_id}"];
                    DB::table('errors')->insert($error_data);
                    // Token is not valid anymore :( 
                    $token->invalidate();
                }
            } catch(\Exception $e) {
                $error_data = ['type' => 'retweet', 'message' => $e->getMessage()];
                DB::table('errors')->insert($error_data);
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
        // Choose campaign type to start only if we have tokens to process
        if ( ! $this->tokens->isEmpty()) {        
            if ($campaign->isPost()) {
                $this->sendPostRequests();
            } elseif ($campaign->isLike()) {
                $this->sendLikeRequests();
            } elseif ($campaign->isRetweet()) {
                $this->sendRetweetRequests();
            } else {
                throw new \Exception('Unknown campaign type!');
            }
        }
        // Tell the world this worker has finished :)
        event(new WorkerFinished($this->worker));
    }
}
