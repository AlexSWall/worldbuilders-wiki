<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class InfoboxToken extends BaseToken
{
	private $infoboxType;
	private $values;

	public function __construct( string $infoboxType, array $values = [] )
	{
		$this->infoboxType = $infoboxType;
		$this->values = $values;
	}

	public function toHtml(): string
	{
		return '';
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'infobox-type' => $this->infoboxType,
			'values' => $this->values
		];
	}
}
