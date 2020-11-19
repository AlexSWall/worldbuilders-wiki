<?php declare(strict_types=1);

namespace App\WikitextConversion\Tokens;

class ClosingTagToken extends BaseTagToken
{
	public function toHTML(): string
	{
		return '</' . $this->getName() . '>';
	}
}