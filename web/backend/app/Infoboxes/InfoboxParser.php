<?php declare( strict_types = 1 );

namespace App\Infoboxes;

class InfoboxParser
{
	static $logger;

	public static function parse( string $infoboxStructureText ): array
	{
		self::$logger->addInfo('Parsing infobox structure text');

		$grammarParser = Grammar::getNewGrammarParser();
		$parseResult = $grammarParser->parse($infoboxStructureText);

		self::$logger->addInfo('Finished parsing; returning array of items');
		return $parseResult;
	}

	public static function checkParse( string $wikitext ) : bool
	{
		self::$logger->addInfo('Checking infobox structure text parses');

		try
		{
			self::parse($wikitext);
			self::$logger->addInfo('Infobox structure text parses');
			return true;
		}
		catch (\Exception $e)
		{
			self::$logger->addInfo('Infobox structure text does not parse');
			return false;
		}
	}
}
