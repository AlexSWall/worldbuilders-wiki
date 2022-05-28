<?php

declare(strict_types=1);

namespace App\WikitextConversion;

use Wikimedia\WikiPEG\SyntaxError;

class WikitextParser
{
	public static \App\Logging\Logger $logger;

	/**
	* @return array Returns an iterable of BaseToken instances, representing the
	*     wikitext.
	 */
	public static function parse( string $wikitext ): array
	{
		self::$logger->info( 'Parsing wikitext' );

		$grammarParser = Grammar::getNewGrammarParser();

		try
		{
			self::$logger->info('Trying to parse: <<<' . $wikitext . '>>>' );
			$parseResult = $grammarParser->parse( $wikitext );
		}
		catch ( SyntaxError $e )
		{
			self::$logger->info('Failed to parse infobox structure: ' . $e->getMessage() );
			self::$logger->dump(json_encode($e->jsonSerialize()));
			return null;
		}

		self::$logger->info( 'Post-processing parsed wikitext tokens' );
		$tokensArray = self::postProcess( $parseResult );

		self::$logger->info( 'Finished parsing; returning tokens' );
		return $tokensArray;
	}

	private static function postProcess( array $parseResult ): array
	{
		return $parseResult;
	}

	public static function checkParse( string $wikitext ): bool
	{
		self::$logger->info( 'Checking wikitext parses' );

		try {
			self::parse( $wikitext );
			self::$logger->info( 'Wikitext parses' );
			return true;
		} catch ( \Exception $_e )
		{
			self::$logger->info( 'Wikitext does not parse' );
			return false;
		}
	}
}
