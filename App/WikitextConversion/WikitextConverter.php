<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	public function __construct()
	{
	}

	public function convertWikitextToHTML( string $wikitext ): string
	{
		$wikitextParser = new WikitextParser();
		$htmlBuilder = new HTML5Builder();

		$tokens = $wikitextParser->parse($wikitext);
		$html = $htmlBuilder->build($tokens);

		return $html;
	}
}