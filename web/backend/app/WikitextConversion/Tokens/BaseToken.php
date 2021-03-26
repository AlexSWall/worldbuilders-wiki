<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

/*
 * Inheritance hierarchy:
 *    BaseToken
 *       BasePlainToken
 *          EndOfFileToken
 *          NewLineToken
 *       BaseTagToken
 *          ClosingTagToken
 *          OpeningTagToken
 *          SelfClosingTagToken
 *       MetaToken
 *       TextToken
 */
abstract class BaseToken implements \JsonSerializable
{
	public function getName(): string
	{
		return $this->getType();
	}

	public function getType(): string
	{
		$classParts = explode( '\\', get_class( $this ) );
		return end( $classParts );
	}

	abstract public function toHTML(): string;
	abstract public function jsonSerialize(): array;
}
