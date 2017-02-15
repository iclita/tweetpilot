<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Campaign;
use Illuminate\Support\Collection;
use App\Jobs\CampaignPublish;

class Worker extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'workers';

    /**
     * The tokens to be processed.
     *
     * @var Collection(App\Token)
     */
    protected $tokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'is_synced',
    	'has_finished',
    	'resume_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_synced' => 'boolean',
        'has_finished' => 'boolean',
    ];

    /**
     * Get queue name for this worker
     *
     * @return string
     */
    public function getQueue()
    {
    	return "campaign-{$this->campaign->id}-worker-{$this->id}";
    }

    /**
     * Change synced state.
     *
     * @return void
     */
    public function toggleSynced()
    {
        $this->is_synced = ! $this->is_synced;
        $this->save();
    }

    /**
     * Show a graphical display of the campaign's Synced state.
     *
     * @return string
     */
    public function displaySynced() 
    {
        if ($this->is_synced) {
            return '<i style="font-size:25px;color:#449D44;" class="fa fa-check" aria-hidden="true"></i>';
        } else {
            return '<i style="font-size:25px;color:#C9302C;" class="fa fa-times" aria-hidden="true"></i>';
        }
    }

    /**
     * Show a graphical display of the campaign's finished state.
     *
     * @return string
     */
    public function displayFinished() 
    {
        if ($this->has_finished) {
            return '<i style="font-size:25px;color:#449D44;" class="fa fa-check" aria-hidden="true"></i>';
        } else {
            return '<i style="font-size:25px;color:#C9302C;" class="fa fa-times" aria-hidden="true"></i>';
        }
    }

    /**
     * Attach a collection of tokens to this worker.
     *
     * @param Illuminate\Support\Collection $tokens
     * @return void
     */
    public function attach(Collection $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Start working on a job.
     *
     * @return void
     */
    public function start()
    {
    	// Update worker state and set it as running (not finished)
    	$this->update(['has_finished' => false]);
    	// Dispatch the job for background processing
    	$job = (new CampaignPublish($this, $this->tokens))->onQueue($this->getQueue());
    	dispatch($job);
    }

    /**
     * Stop the current worker.
     *
     * @return void
     */
    public function stop()
    {
        // Update worker state and set it as finished and done
        $this->update(['resume_token' => 0, 'has_finished' => true]);
    }

    /**
     * Set a token marker for the worker to start after being resumed.
     *
     * @param App\Token $token
     * @return void
     */
    public function setResumeToken(Token $token)
    {
    	$this->update(['resume_token' => $token->id]);
    }

    /**
     * Get only synced workers.
     *
     * @return QueryBuilder
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true);
    }

    /**
     * Check if this token has already been processed by this worker.
     *
     * @param App\Token $token
     * @return bool
     */
    public function processedToken(Token $token)
    {
    	return $this->resume_token > $token->id;
    }   

    /**
     * Worker belongs to a Campaign.
     *
     * @return BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
