<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'title',
        'slug',
    ];

    /**
     * The number of video articles displayed per page.
     *
     * @var int
     */
    const PAGINATION = 16;

    /**
     * Get the video youtube URL.
     *
     * @return string
     */
    public function getUrl()
    {
    	return 'https://www.youtube.com/watch?v=' . $this->slug;
    }

    /**
     * Get the youtube video image preview.
     *
     * @return string
     */
    public function getImagePreview()
    {   
        return 'https://i.ytimg.com/vi/' . $this->slug . '/hqdefault.jpg';
    }
}
