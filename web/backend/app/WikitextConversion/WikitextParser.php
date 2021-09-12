<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextParser
{
	static \App\Logging\Logger $logger;

	/**
	 * @return Returns an iterable of BaseToken instances, representing the wikitext.
	 */
	public static function parse( string $wikitext ): array
	{
		self::$logger->info('Parsing wikitext');

		$grammarParser = Grammar::getNewGrammarParser();
		$parseResult = $grammarParser->parse($wikitext);

		self::$logger->info('Post-processing parsed wikitext tokens');
		$tokensArray = self::postProcess($parseResult);

		self::$logger->info('Finished parsing; returning tokens');
		return $tokensArray;
	}

	private static function postProcess( array $parseResult ): array
	{
		return $parseResult;
	}

	public static function checkParse( string $wikitext ): bool
	{
		self::$logger->info('Checking wikitext parses');

		try
		{
			self::parse($wikitext);
			self::$logger->info('Wikitext parses');
			return true;
		}
		catch (\Exception $e)
		{
			self::$logger->info('Wikitext does not parse');
			return false;
		}
	}
}
