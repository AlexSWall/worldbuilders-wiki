<?php

declare(strict_types=1);

namespace App\Infoboxes;

use Wikimedia\WikiPEG\SyntaxError;

class InfoboxParser
{
	public static \App\Logging\Logger $logger;

	public static function parse( string $infoboxStructureText ): ?array
	{
		self::$logger->info( 'Parsing infobox structure text' );

		$grammarParser = Grammar::getNewGrammarParser();

		try
		{
			$parseResult = $grammarParser->parse( $infoboxStructureText );
		}
		catch ( SyntaxError $e )
		{
			self::$logger->info('Failed to parse infobox structure: ' . $e->getMessage() );
			self::$logger->info('Was trying to parse: <<<' . $infoboxStructureText . '>>>' );
			self::$logger->dump(json_encode($e->jsonSerialize()));
			return null;
		}

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
			self::$logger->info( 'Infobox structure text does not parse; error message: ' . $e->getMessage());
			return false;
		}
	}
}
