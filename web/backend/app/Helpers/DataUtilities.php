<?php declare( strict_types = 1 );

namespace App\Helpers;

class DataUtilities
{
	/**
	 * Decodes a JSON array or object from a string. If the string does not
	 * represent a JSON array or object, null is returned instead.
	 */
	public static function decodeJSONArray(string $str): mixed
	{
		$json = json_decode($str);
		if ($json && $str != $json)
			return $json;
		return null;
	}

	public static function isNonEmptyString(mixed $var): bool
	{
		return is_string($var) && $var !== '';
	}
}
