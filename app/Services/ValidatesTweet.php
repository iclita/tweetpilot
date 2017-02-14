<?php

namespace App\Services;

trait ValidatesTweet
{
    /**
     * Validate a tweet by length
     *
     * @param array $tweet
     * @return bool
     */
    public function tweetIsValid(array $tweet)
    {
        $message = $tweet['message'];
        $link = $tweet['link'];
        // This is the full post on Twitter
        $post = $message . ' ' . urlencode($link);
        // Check to see if post has less than 140 characters specific to Twitter
        return strlen($post) <= 140;
    }
}
