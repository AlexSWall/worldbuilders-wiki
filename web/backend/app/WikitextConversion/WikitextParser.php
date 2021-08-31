<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextParser
{
	static $logger;

	/**
	 * @return string Returns an iterable of BaseToken instances, representing the wikitext.
	 */
	public static function parse( string $wikitext ): array
	{
		self::$logger->addInfo('Parsing wikitext');

		$grammarParser = Grammar::getNewGrammarParser();
		$parseResult = $grammarParser->parse($wikitext);

		self::$logger->addInfo('Post-processing parsed wikitext tokens');
		$tokensArray = self::postProcess($parseResult);

		self::$logger->addInfo('Finished parsing; returning tokens');
		return $tokensArray;
	}

	private static function postProcess( $parseResult )
	{
		return $parseResult;
	}

	public static function checkParse( string $wikitext ) : bool
	{
		self::$logger->addInfo('Checking wikitext parses');

		try
		{
			self::parse($wikitext);
			self::$logger->addInfo('Wikitext parses');
			return true;
		}
		catch (Exception $e)
		{
			self::$logger->addInfo('Wikitext does not parse');
			return false;
		}
	}
}
