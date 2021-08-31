<?php declare( strict_types = 1 );

namespace App\Infoboxes;

use App\WikitextConversion\Tokens\TextToken;

class InfoboxUtilities
{
	/**
	 * Obtains the required entry value for the entry key from the array of
	 * arguments, and then checks its type with the typeCheckFunction parameter,
	 * if provided.
	 */
	public static function getEntryValueForKey(array $args, string $entryKey, ?callable $typeCheckFunction = null)
	{
		if ( !array_key_exists($entryKey, $args) )
			return null;

		// Key exists in args; obtain its value;
		$entryValue = $args[$entryKey];

		if( $typeCheckFunction && ! $typeCheckFunction( $entryValue ) )
			throw new \InvalidArgumentException('Entry\'s value failed to satisfy type checking function.');

		return $entryValue;
	}

	/**
	 * Unpacks an array of length 1 containing only a TextToken into a string.
	 * If the argument does not satisfy this, it throws.
	 */
	public static function unpackTextToken(array $tokens) : string
	{
		if ( count($tokens) === 1 && is_a( $tokens[0], TextToken::class ) )
			return $tokens[0]->toHtml();
		else
			throw new \InvalidArgumentException('Input is not a length-one array containing only a TextToken.');
	}
}
