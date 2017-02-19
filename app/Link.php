<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'description',
        'url',
        'updated_at',
    ];

    /**
     * The number of links displayed per page.
     *
     * @var int
     */
    const PAGINATION = 20;
}
