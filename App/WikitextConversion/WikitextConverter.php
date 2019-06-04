<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	public function __construct()
	{
	}

	public static function convertWikitextToHtmlBlocks( string $wikitext, string $pageName = '' ): string
	{
		$wikitextParser = new WikitextParser();
		$htmlBuilder = new HTML5Builder();

		$tokens = $wikitextParser->parse($wikitext);
		$htmlBlocks = $htmlBuilder->buildAndGetHtmlBlocks($tokens);

		return $htmlBlocks;
	}
}