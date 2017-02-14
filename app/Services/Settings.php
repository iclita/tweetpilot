<?php

namespace App\Services;

use Redis;

class Settings
{
    /**
     * Set a key in the campaign settings.
     *
     * @param string $key
     * @return void
     */
    public static function set($key, $value)
    {
        switch ($key) {
            case 'is_auto':
                Redis::set('is_auto', $value);
                break;
            case 'publish_interval':
                Redis::set('publish_interval', $value);
                break;
            case 'growth_percentage':
                Redis::set('growth_percentage', $value);
                break;
            case 'num_workers':
                Redis::set('num_workers', $value);
                break;
            default:
                throw new \Exception("Unknown settings key: {$key}");
                break;
        }
    }

    /**
     * Get a key from the campaign settings.
     *
     * @param string $key
     * @return void
     */
    public static function get($key)
    {
        switch ($key) {
            case 'is_auto':
                $value = Redis::exists('is_auto') ? Redis::get('is_auto') : false;
                break;
            case 'publish_interval':
                $value = Redis::exists('publish_interval') ? Redis::get('publish_interval') : 24;
                break;
            case 'growth_percentage':
                $value = Redis::exists('growth_percentage') ? Redis::get('growth_percentage') : 0;
                break;
            case 'num_workers':
                $value = Redis::exists('num_workers') ? Redis::get('num_workers') : 1;
                break;
            default:
                throw new \Exception("Unknown settings key: {$key}");
                break;
        }

        return $value;       
    }
}
