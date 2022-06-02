<?php

declare(strict_types=1);

namespace App\Validation;

use App\Helpers\DataUtilities;

class QuickNavigationSetValidation
{
	public static function isQuickNavigationSetDataFormatRule( ?string $failureString = null ): callable
	{
		$failureString = $failureString ?: "JSON is not in the expect form for Quick Navigation Set data.";

		return function ( string $input ) use ( $failureString ): ?string
		{
			if ( ! self::isQuickNavigationSet( $input ) )
				return $failureString;

			return null;
		};
	}

	/**
		QuickNavigationSet :: { QuickNavIdFormat() => [ QuickNavEntryDictionaryFormat() ... ] ... }

			QuickNavIdFormat :: AnyText

			QuickNavEntryDictionaryFormat :: FolderEntry | WikipageEntry | CategoryEntry

				FolderEntry :: {
					"type": "folder",
					"display-name": AnyText,
					"value": [ QuickNavEntryDictionaryFormat() ... ]
				}

				WikipageEntry :: {
					"type": "wikipage",
					"display-name": AnyText,
					"value": WikipageUrlPath
				}

				CategoryEntry :: {
					"type": "category",
					"display-name": AnyText,
					"value": CategoryName
				}
	*/
	private static function isQuickNavigationSet( string $data ): bool
	{
		$data = json_decode( $data, true );

		if ( ! $data || ! is_array($data) ) {
			return false;
		}

		foreach ( $data as $key => $value )
		{
			
			if ( ! DataUtilities::isNonEmptyString( $key ) )
				return false;

			if ( ! self::isQuickNavigationEntry( $value ) )
				return false;
		}

	}

	const EXPECTED_KEYS = ['type', 'display-name', 'value'];

	private static function isQuickNavigationEntry( array $entry ): bool
	{
		// First, ensure we have precisely the expected keys.
		if ( count($entry) !== count(QuickNavigationSetValidation::EXPECTED_KEYS) )
			return false;

		foreach ( QuickNavigationSetValidation::EXPECTED_KEYS as $expectedKey )
			if ( ! isset( $entry[$expectedKey] ) )
				return false;

		// Now, ensure our values conform to the expected values.
		$type = $entry['type'];  // Must be one of a few specific strings.
		$displayValue = $entry['display-name'];  // Must be a (possibly-empty) string.
		$value = $entry['value'];  // Validation depends on type.

		// Validate $type and $display-value.
		if ( ! is_string($type) || ! is_string($displayValue) )
			return false;

		// Validate $value.
		switch ( $type )
		{
			case 'folder':

				// Ensure $value is a sequential array.
				if ( ! is_array($value) || array_keys($value) !== range(0, count($value) - 1) )
					return false;

				$innerEntries = $value;

				// Ensure the array contains only Quick Navigation entries.
				foreach ( $innerEntries as $innerEntry )
					if ( ! self::isQuickNavigationEntry( $innerEntry ) )
						return false;

				// We have a sequential array of only Quick Navigation entries;
				// success.
				break;

			case 'wikipage':

				if ( ! is_string($value) )
					return false;

				// We have a string $value, which refers to a wikipage path;
				// success.
				break;

			case 'category':

				// Not implemented yet.
				return false;

			default:
				// We did not have a valid type.
				return false;
		}

		return true;
	}

	public static function quickNavigationSetDataRules(): array
	{
		return [ Rules::required(), Rules::length( 2, null ), Rules::json(), self::isQuickNavigationSetDataFormatRule() ];
	}
}
