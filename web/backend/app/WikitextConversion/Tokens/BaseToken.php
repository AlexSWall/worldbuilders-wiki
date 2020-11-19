<?php declare( strict_types = 1 );

namespace App\WikitextConversion\Tokens;

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

	public static function getToken( array $tokenData ): ?BaseToken
	{
		if ( !isset( $tokenData['type'] ) )
			return null;

		switch ( $tokenData['type'] ) {
				case "TagTk":
				    return new OpeningTagToken( $tokenData['name'], $tokenData['attributes'] );
				case "EndTagTk":
				    return new ClosingTagToken( $tokenData['name'], $tokenData['attributes'] );
				case "SelfclosingTagTk":
				    return new SelfClosingTagToken( $tokenData['name'], $tokenData['attributes'] );
				case "NlTk":
				    return new NewLineToken();
				case "EOFTk":
					return new EndOfFileToken();
				default:
				    return null;
			}
	}

	abstract public function toHTML(): string;
	abstract public function jsonSerialize(): array;
}