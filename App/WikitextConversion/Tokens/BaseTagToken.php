<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

abstract class BaseTagToken extends BaseToken
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

	/**
	 * @return string The tag's attributes as a string for use within a HTML string.
	 */
	public function getTagAttributesString(): string
	{
		$attributePairStrings = array();

		foreach( $this->attributes as $key => $value )
		{
			$pairString = '';
			if ( is_bool($value) && $value )
				$pairString = $key;
			elseif ( is_string($value) )
			{
				if ( preg_match( '/\s/', $value ) )
				    $pairString .= '=\'' . $value . '\'';
				else
				    $pairString .= '=' . $value;
			}

			if ( $pairString !== '' )
				$attributePairStrings[] = $pairString;
		}

		$attributesString = implode(' ', $attributePairStrings);
		return $attributesString;
	}

	public function getAttributes(): array
	{
		/* Arrays are returned by (shallow?) value. */
		return $this->attributes;
	}

	public function getAttributeValueFor( string $key )
	{
		return $this->attributes[$key];
	}

	private function setAttribute( string $key, $value ): void
	{
		$this->attributes[$key] = $value;
	}

	public function addAttribute( string $key, $value ): void
	{
		if ( array_key_exists($key, $this->attributes) )
			throw new \UnexpectedValueException('Attribute to add is already an attribute of the token.');
		$this->setAttribute($key, $value);
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