<?php

declare(strict_types=1);

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

	private function removeSpecialHtmlTagCharacters( string $s ): string
	{
		return str_replace( [ '\'', '"', '`', '=', '<', '>'], '', $s );
	}

	/**
	 * @return string The tag's attributes as a string for use within a HTML string.
	 */
	public function getTagAttributesString(): string
	{
		$attributePairStrings = array();

		foreach ( $this->attributes as $key => $value )
		{
			$safeKey = $this->removeSpecialHtmlTagCharacters( $key );

			$pairString = '';
			if ( is_bool( $value ) && $value ) {
				$pairString = $safeKey;
			} else {
				$strValue = strval( $value );
			}
				$pairString .= $safeKey . '=\'' . $this->removeSpecialHtmlTagCharacters( $strValue ) . '\'';

			if ( $pairString !== '' ) {
				$attributePairStrings[] = $pairString;
			}
		}

		$attributesString = implode( ' ', $attributePairStrings );
		return $attributesString;
	}

	public function hasAttributes(): bool
	{
		return sizeof( $this->attributes ) > 0;
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
