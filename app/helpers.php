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