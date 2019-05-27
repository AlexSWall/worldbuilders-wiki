<?php declare( strict_types = 1 );

namespace App\WikitextConversion;

class HTML5Builder
{
	private $html;

	public function __construct()
	{
	}

	public function parse( $tokens ): string
	{
		$this->html = '';

		foreach ( $tokens as $token )
            processToken($token);

		return $this->html;
	}

	private function processToken( object $token ): void
	{
		assert( is_a($token, App\WikitextConversion\Tokens\BaseToken) );
    	$this->html .= $token.toHTML();
	}
}