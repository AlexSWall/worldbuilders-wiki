<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

abstract class BasePlainToken extends BaseToken
{
	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType()
		];
	}
}