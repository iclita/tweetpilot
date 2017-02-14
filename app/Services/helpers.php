<?php

if ( ! function_exists('short'))
{
	/**
	 * Get the short form of a string.
	 *
	 * @param  string  $string
	 * @param  int  $length
	 * @param  string  $append
	 * @return string
	 */
	function short($string, $length=40, $append='...')
	{
		if (mb_strlen($string.$append) > $length) {
			$endPosition = $length - mb_strlen($append);
			$newString = mb_substr($string, 0, $endPosition).$append;
		}else{
			$newString = $string;
		}

		return $newString;
	}
}

if ( ! function_exists('settings'))
{
	/**
	 * Get the campaign general settings by key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	function settings($key)
	{
		$value = '';

		switch ($key) {
			case 'is_auto':
				$value = \App\Services\Settings::get('is_auto');
				break;
			case 'publish_interval':
				$value = \App\Services\Settings::get('publish_interval');
				break;
			case 'growth_percentage':
				$value = \App\Services\Settings::get('growth_percentage');
				break;
			case 'num_workers':
				$value = \App\Services\Settings::get('num_workers');
				break;
			default:
				throw new \Exception("Unknown settings key: {$key}");
				break;
		}

		return $value;
	}
}