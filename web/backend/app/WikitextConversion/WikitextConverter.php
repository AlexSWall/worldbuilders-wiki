<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	private $htmlBuilder;

	private $htmlBlocks;
	private $html;

	public function __construct(string $wikitext)
	{
		$this->htmlBuilder = new HTML5Builder();

		$tokens = WikitextParser::parse($wikitext);

		$this->htmlBuilder->build($tokens);

		$this->htmlBlocks = $this->htmlBuilder->getHtmlBlocks();
		$this->html = null;
	}

	public function getHtmlBlocks(): array
	{
		return $this->htmlBlocks;
	}

	public function getHtml(): string
	{
		// Set cache member variable if necessary
		if ($this->html === null)
			$this->html = $this->htmlBuilder->getHtml();

		// Read from cache member variable
		return $this->html;
	}
}
