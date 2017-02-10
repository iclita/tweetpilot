<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;

class Token extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'user_id',
    	'screen_name',
        'access_token',
    	'access_token_secret',
    	'valid',
    ];

    /**
     * Save/update Token in DB by attaching it to a Website.
     *
     * @return App\Token
     */
    public static function generate(array $data, Website $website)
    {
        $token_data = [
            'user_id' => $data['user_id'],
            'screen_name' => $data['screen_name'],
            'access_token' => $data['oauth_token'],
            'access_token_secret' => $data['oauth_token_secret'],
        ];
        // Check if this user is already present in the DB
        // If present, update the existing token, otherwise create a new token
        $old_token = static::where('user_id', $data['user_id'])->orWhere('screen_name', $data['screen_name'])
                                                               ->first();
        if ( ! is_null($old_token)) {
            $old_token->update($token_data);
        } else {          
            $new_token = new static($token_data);
            $website->tokens()->save($new_token);
        }
    }   

    /**
     * Token belongs to a Website.
     *
     * @return BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
