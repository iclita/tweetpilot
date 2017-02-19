<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;
use App\Worker;
use Redis;
use DB;

class Campaign extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'campaigns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'type',
    	'status',
        'custom_message',
    	'custom_link',
    	'post_id',
    	'resume_token',
    	'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Generate all workers associated with this campaign.
     *
     * @return void
     */
    private function generateWorkers()
    {
        $num_workers = (bool)Redis::exists('num_workers') ? (int)Redis::get('num_workers') : 1;

        $workers = [];
        for ($i=1;$i<=$num_workers;$i++) {
            $workers[] = new Worker();
        }

        $this->workers()->saveMany($workers);
    }

    /**
     * Reset all workers associated with this campaign.
     *
     * @return void
     */
    private function resetWorkers()
    {
        DB::table('workers')->where('campaign_id', $this->id)
                            ->update(['resume_token' => 0, 'has_finished' => true]);
    }

    /**
     * Run all workers.
     *
     * @return void
     */
    private function runWorkers()
    {
        // Get total number of valid tokens
        $num_tokens = $this->website->getValidTokensCount();
        // Start everything if we have tokens to process
        if ($num_tokens > 0) {        
            // Get all the workers that have been synced with Forge
            $workers = $this->workers()->synced()->get();
            // Get number of workers
            $num_workers = $workers->count();
            // Abort if no workers detected for this campaign
            if ($num_workers === 0) {
                throw new \Exception("Campaign {$this->id} has no workers!");
            }
            // Calculate how much load every worker should cary
            $worker_load = ceil($num_tokens/$num_workers);
            // Distribute the tasks uniformly based on worker load
            // Shuffle the tokens before 
            for ($i=0; $i<$num_workers; $i++) {
                $offset = $i * $worker_load;
                $tokens = $this->website->tokens()->valid()
                                                  ->skip($offset)
                                                  ->take($worker_load)
                                                  ->get()
                                                  ->shuffle();
                $worker = $workers[$i];
                // Start current worker and process tokens
                $worker->start()
                       ->process($tokens);      
            }
        }
    }

    /**
     * Create a new campaign by associating it with a free(available) website.
     *
     * @param array $data
     * @return void
     */
    public static function make(array $data)
    {
        $website_id = (int) $data['website'];
        $website = Website::find($website_id);
        $data = array_except($data, ['website']);
        $campaign = $website->campaign()->create($data);
        // Generate all associated workers
        $campaign->generateWorkers();
    }

    /**
     * Check if campaign is stopped.
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->status === 'stopped';
    }

    /**
     * Check if campaign is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->status === 'running';
    }

    /**
     * Check if campaign is paused.
     *
     * @return bool
     */
    public function isPaused()
    {
        return $this->status === 'paused';
    }

    /**
     * Check if campaign is of type post.
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->type === 'post';
    }

    /**
     * Check if campaign is of type like.
     *
     * @return bool
     */
    public function isLike()
    {
        return $this->type === 'like';
    }

    /**
     * Check if campaign is of type retweet.
     *
     * @return bool
     */
    public function isRetweet()
    {
        return $this->type === 'retweet';
    }

    /**
     * Change active state.
     *
     * @return void
     */
    public function toggleActive()
    {
        $this->active = ! $this->active;
        $this->save();
    }

    /**
     * Change current status.
     *
     * @param string $status
     * @return void
     */
    public function changeStatusTo($status)
    {
        $this->update(['status' => $status]);
    }

    /**
     * Show a graphical display of the campaing's type.
     *
     * @return string
     */
    public function displayType() 
    {
        if ($this->isPost()) {
            return '<i style="font-size:20px;color:#1DA1F2;" class="fa fa-twitter" aria-hidden="true"></i>';
        } elseif ($this->isLike()) {
            return '<i style="font-size:20px;color:#E2264D;" class="fa fa-heart" aria-hidden="true"></i>';
        } elseif ($this->isRetweet()) {
            return '<i style="font-size:20px;color:#50D9A3;" class="fa fa-retweet" aria-hidden="true"></i>';
        } else {
            throw new \Exception('Unknown campaign type!');
        }
    }

    /**
     * Show a graphical display of the campaing's active state.
     *
     * @return string
     */
    public function displayActive() 
    {
        if ($this->active) {
            return '<i style="font-size:25px;color:#449D44;" class="fa fa-check" aria-hidden="true"></i>';
        } else {
            return '<i style="font-size:25px;color:#C9302C;" class="fa fa-times" aria-hidden="true"></i>';
        }
    }

    /**
     * Show a graphical display of the campaing's action buttons.
     *
     * @return string
     */
    public function displayAction()
    {
        if ($this->isStopped()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action' style='display:none;'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action' style='display:none;'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-warning btn-sm resume-campaign campaign-action' style='display:none;'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";
        } elseif ($this->isRunning()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action' style='display:none;'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-warning btn-sm resume-campaign campaign-action' style='display:none;'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";           
        } elseif ($this->isPaused()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action' style='display:none;'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action' style='display:none;'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action' style='display:none;'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-warning btn-sm resume-campaign campaign-action'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";            
        } else {
            throw new \Exception('Unknown campaign action!');
        }
    }

    /**
     * Show a graphical display of the campaing's current status.
     *
     * @return string
     */
    public function displayStatus() 
    {
        if ($this->isStopped()) {
            return "<i data-status={$this->id} style='font-size:20px;color:#C9302C;' class='fa fa-circle stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#2579A9;' class='fa fa-clock-o paused-status-icon campaign-status' aria-hidden='true'></i>";
        } elseif ($this->isRunning()) {
            return "<i data-status={$this->id} style='display:none;font-size:20px;color:#C9302C;' class='fa fa-circle stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#2579A9;' class='fa fa-clock-o paused-status-icon campaign-status' aria-hidden='true'></i>";
        } elseif ($this->isPaused()) {
            return "<i data-status={$this->id} style='display:none;font-size:20px;color:#C9302C;' class='fa fa-circle stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='font-size:20px;color:#2579A9;' class='fa fa-clock-o paused-status-icon campaign-status' aria-hidden='true'></i>";
        } else {
            throw new \Exception('Unknown campaign status!');
        }
    }

    /**
     * Start campaign. Go go go:)
     *
     * @return void
     */
    public function start()
    {
        // First reset all the workers so that resume token is disabled
        $this->resetWorkers();
        // Set campaign on running status
        $this->changeStatusTo('running');
        // Run all workers
        $this->runWorkers();
    }

    /**
     * Stop campaign. Go go go:)
     *
     * @return void
     */
    public function stop()
    {
        // Set campaign on stop status
        $this->changeStatusTo('stopped');
        // Reset all the workers so that resume token is disabled
        $this->resetWorkers();
    }

    /**
     * Pause campaign. Stop here:)
     *
     * @return void
     */
    public function pause()
    {
        // Set campaign on pause status
        $this->changeStatusTo('paused');
    }

    /**
     * Resume campaign. Go go go:)
     *
     * @return void
     */
    public function resume()
    {
        // Set campaign on running status
        $this->changeStatusTo('running');
        // Practically, resume is the same as start without the reset phase
        $this->runWorkers();
    }

    /**
     * Check if a campaign has tokens to start
     *
     * @return bool
     */
    public function shouldStart()
    {
        return DB::table('tokens')->join('websites', 'tokens.website_id', '=', 'tokens.id')
                                  ->join('campaigns', 'campaigns.website_id', '=', 'campaigns.id')
                                  ->where('tokens.valid', true)
                                  ->where('campaigns.id', $this->id)
                                  ->exists();
    }

    /**
     * Check if all workers have finished to stop the campaign
     *
     * @return bool
     */
    public function shouldStop()
    {
        return !DB::table('workers')->where('campaign_id', $this->id)
                                     ->where('has_finished', false)
                                     ->exists();
    }

    /**
     * Check if this campaign has any custom data associated
     *
     * @return bool
     */
    public function isCustom()
    {
        return !empty($this->custom_message) && !empty($this->custom_link);
    }

    /**
     * Get only active campaigns.
     *
     * @return QueryBuilder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Campaign belongs to a Website.
     *
     * @return BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * A Campaign has many Workers.
     *
     * @return HasMany
     */
    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}
