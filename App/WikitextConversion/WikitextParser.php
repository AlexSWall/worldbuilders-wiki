<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class WikitextParser
{
	private $grammarClass;

	public function __construct( $grammarClass )
	{
		$this->grammarClass = $grammarClass;
	}

	/**
	 * @return string Returns an iterable of BaseToken instances, representing the wikitext.
	 */
	public function parse( string $wikitext )
	{
		$grammar = new $this->grammarClass( $wikitext ) ;
		$parseResult = $grammar->match_Start();
		$tokens = array();

		if ( $parseResult !== FALSE )
			$tokens = $parseResult['val'];

		print_r( $tokens ) ;
		return $tokens;
	}
}