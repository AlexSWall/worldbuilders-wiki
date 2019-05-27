<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class TextToken extends BaseToken
{
	private $text;

	public function __construct( string $text )
	{
		$this->text = $text;
	}

	public function toHTML(): string
	{
		return $text;
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'text' => $this->text
		];
	}
}