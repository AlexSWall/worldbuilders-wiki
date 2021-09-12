<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextConverter
{
	private $tokenProcessor;

	private array $htmlBlocks;
	private ?string $html;

	public function __construct( string $wikitext )
	{
		$this->tokenProcessor = new TokenProcessor();

		$tokens = WikitextParser::parse($wikitext);

		$this->htmlBlocks = $this->tokenProcessor->process($tokens, 'top-level');

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
			$this->html = $this->tokenProcessor->getHtml();

		// Read from cache member variable
		return $this->html;
	}
}
