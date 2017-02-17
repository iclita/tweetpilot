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
            case 'last_run':
                Redis::set('last_run', $value);
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
     * @param string $default
     * @return void
     */
    public static function get($key, $default=null)
    {
        switch ($key) {
            case 'is_auto':
                $value = static::has('is_auto') ? Redis::get('is_auto') : $default; // $default=false
                break;
            case 'publish_interval':
                $value = static::has('publish_interval') ? Redis::get('publish_interval') : $default; // $default=24
                break;
            case 'growth_percentage':
                $value = static::has('growth_percentage') ? Redis::get('growth_percentage') : $default; // $default=0
                break;
            case 'num_workers':
                $value = static::has('num_workers') ? Redis::get('num_workers') : $default; // $default=1
                break;
            case 'last_run':
                $value = static::has('last_run') ? Redis::get('last_run') : $default; // $default=1
                break;
            default:
                throw new \Exception("Unknown settings key: {$key}");
                break;
        }

        return $value;       
    }

    /**
     * Check if a key exists in campaign settings.
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return (bool) Redis::exists($key);
    }
}
