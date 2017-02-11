<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;

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
        $website->campaign()->create($data);
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
     * Show a graphical display of the campaing's type.
     *
     * @return string
     */
    public function showType() 
    {
        if ($this->isPost()) {
            return '<i style="font-size:20px;color:#1DA1F2;" class="fa fa-twitter" aria-hidden="true"></i>';
        } elseif ($this->isLike()) {
            return '<i style="font-size:20px;color:#E2264D;" class="fa fa-heart" aria-hidden="true"></i>';
        } elseif ($this->isRetweet()) {
            return '<i style="font-size:20px;color:#50D9A3;" class="fa fa-retweet" aria-hidden="true"></i>';
        }else {
            throw new \Exception('Unknown campaign type!');
        }
    }

    /**
     * Show a graphical display of the campaing's active state.
     *
     * @return string
     */
    public function showActive() 
    {
        if ($this->active) {
            return '<i style="font-size:25px;color:#449D44;" class="fa fa-check" aria-hidden="true"></i>';
        } else{
            return '<i style="font-size:25px;color:#C9302C;" class="fa fa-times" aria-hidden="true"></i>';
        }
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
}
