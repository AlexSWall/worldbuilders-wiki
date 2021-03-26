<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class SelfClosingTagToken extends BaseTagToken
{
	public function toHTML(): string
	{
		return '<' . $this->getName() . ($this->hasAttributes() > 0 ? ' ' : '') . $this->getTagAttributesString() . ' />';
	}
}
