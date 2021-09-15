<?php

declare(strict_types=1);

namespace App\Infoboxes;

class InfoboxParser
{
	public static \App\Logging\Logger $logger;

	public static function parse( string $infoboxStructureText ): array
	{
		self::$logger->info( 'Parsing infobox structure text' );

		$grammarParser = Grammar::getNewGrammarParser();
		$parseResult = $grammarParser->parse( $infoboxStructureText );

		self::$logger->info( 'Finished parsing; returning array of items' );
		return $parseResult;
	}

	public static function checkParse( string $wikitext ): bool
	{
		self::$logger->info( 'Checking infobox structure text parses' );

		try {
			self::parse( $wikitext );
			self::$logger->info( 'Infobox structure text parses' );
			return true;
		} catch ( \Exception $e )
		{
			self::$logger->info( 'Infobox structure text does not parse' );
			return false;
		}
	}
}
