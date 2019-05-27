<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextParser
{
	private $grammar;

	public function __construct( object $grammar )
	{
		$this->grammar = $grammar;
	}

	/**
	 * @return string Returns an iterable of BaseToken instances, representing the wikitext.
	 */
	public function parse( string $wikitext )
	{
		return $this->grammar->parse($wikitext);
	}
}