<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Token;

class Post extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'post_id',
    ];

    /**
     * Create a new post belonging to $token.
     *
     * @return void
     */
    public static function make(array $data, Token $token)
    {
        $token->posts()->create($data);
    } 

    /**
     * Post belongs to a Token.
     *
     * @return BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
