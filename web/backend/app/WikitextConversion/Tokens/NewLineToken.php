<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class NewLineToken extends BasePlainToken
{
	public function toHTML(): string
	{
		return "\n";
	}
}