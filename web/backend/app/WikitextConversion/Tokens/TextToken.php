<?php

declare(strict_types=1);

namespace App\WikitextConversion\Tokens;

class TextToken extends BaseToken
{
	private string $text;

	public function __construct( string $text )
	{
		$this->text = $text;
	}

	public function trim(): void
	{
		$this->text = trim( $this->text );
	}

	public function ltrim(): void
	{
		$this->text = ltrim( $this->text );
	}

	public function rtrim(): void
	{
		$this->text = rtrim( $this->text );
	}

	public function toHTML(): string
	{
		return htmlspecialchars( $this->text );
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'text' => $this->text
		];
	}
}
