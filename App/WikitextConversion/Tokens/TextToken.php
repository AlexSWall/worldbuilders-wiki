<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class TextToken extends BaseToken
{
	private $text;

	public function __construct( string $text )
	{
		$this->text = $text;
	}

	public function trim()
	{
		$this->text = trim($this->text);
	}

	public function ltrim()
	{
		$this->text = ltrim($this->text);
	}

	public function rtrim()
	{
		$this->text = rtrim($this->text);
	}

	public function toHTML(): string
	{
		return htmlspecialchars($this->text);
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'text' => $this->text
		];
	}
}