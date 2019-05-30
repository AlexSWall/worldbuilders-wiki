<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextParser
{
	/**
	 * @return string Returns an iterable of BaseToken instances, representing the wikitext.
	 */
	public function parse( string $wikitext )
	{
		$grammarParser = Grammar::getNewGrammarParser() ;
		$parseResult = $grammarParser->parse($wikitext);
		return $parseResult;
	}
}