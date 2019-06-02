<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

class MetaToken extends BaseToken
{
	private $name;
	private $attributes;

	public function __construct( string $name, array $attributes = [] )
	{
		$this->name = $name;
		$this->attributes = $attributes;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getAttributes(): array
	{
		/* Arrays are returned by (shallow?) value. */
		return $this->attributes;
	}

	public function toHtml(): string
	{
		throw new \Exception('Cannot convert a meta token to HTML.');
		return '';
	}

	public function jsonSerialize(): array
	{
		return [
			'type' => $this->getType(),
			'name' => $this->name,
			'attributes' => $this->attributes
		];
	}
}