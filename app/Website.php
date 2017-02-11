<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Token;
use App\Campaign;
use DB;

class Website extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'websites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',
        'description',
    	'url',
    	'app_key',
    	'app_secret',
    ];

    /**
     * Find a website based on the current url.
     *
     * @param string $url
     * @param string $scheme
     * @return App\Website
     */
	public static function findByUrl($url, $scheme='http')
    {
        $url_components = parse_url($url);
        $host = $url_components['host'];
        $website = static::where('url', $host)->first();
        // If we cannot find by host we retry by adding the scheme
        if (is_null($website)) {
            $host = $scheme . '://' . $host;
            $website = static::where('url', $host)->first();
        }
        return $website;
    }

    /**
     * Get the website's full URL including the scheme.
     *
     * @param string $scheme
     * @return string
     */
    public function getFullUrl($scheme='http')
    {
        $host = $this->url;
        $scheme .= '://';
        // If the website starts with http(s):// then there is no need to add it
        if (substr($host, 0, strlen($scheme)) === $scheme) {
            $scheme = '';
        }
        return $scheme . $host;
    }

    /**
     * Get available websites that were not already associated with campaigns.
     *
     * @return array
     */
    public static function getAvailable()
    {
        $associated_ids = DB::table('campaigns')->pluck('website_id');
        return DB::table('websites')->whereNotIn('id', $associated_ids)
                                    ->pluck('url', 'id')
                                    ->toArray();
    } 

    /**
     * A Website has many Tokens.
     *
     * @return HasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * A Website has one Campaign.
     *
     * @return HasMany
     */
    public function campaign()
    {
        return $this->hasOne(Campaign::class);
    }
}
