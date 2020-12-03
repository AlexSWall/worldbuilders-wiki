<?php

namespace App\Helpers;

class DataUtilities
{
	/**
	 * Decodes a JSON array or object from a string. If the string does not
	 * represent a JSON array or object, null is returned instead.
	 */
	public static function decodeJSONArray($str)
	{
		$json = json_decode($str);
		if ($json && $str != $json)
			return $json;
		return null;
	}

	public static function isNonEmptyString($var)
	{
		return is_string($var) && $str !== '';
	}
}
