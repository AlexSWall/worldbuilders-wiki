<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class EndOfFileToken extends BasePlainToken
{
	public function toHTML(): string
	{
		return '';
	}
}

