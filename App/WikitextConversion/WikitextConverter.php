<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	private $wikitextParser;
	private $htmlBuilder;

	public function __construct()
	{
		$this->wikitextParser = new WikitextParser();
		$this->htmlBuilder = new HTML5Builder();
	}

	private function parseAndBuild( string $wikitext, string $pageName ): void
	{
		$tokens = $this->wikitextParser->parse($wikitext);

		$this->htmlBuilder->initialise();
		$this->htmlBuilder->build($tokens);
	}

	public function convertWikitextToHtmlBlocks( string $wikitext, string $pageName = '' ): array
	{
		$this->parseAndBuild( $wikitext, $pageName );
		return $this->htmlBuilder->getHtmlBlocks();
	}

	public function convertWikitextToHtml( string $wikitext, string $pageName = '' ): string
	{
		$this->parseAndBuild( $wikitext, $pageName );
		return $this->htmlBuilder->getHtml();
	}
}