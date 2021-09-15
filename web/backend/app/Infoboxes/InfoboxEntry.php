<?php

declare(strict_types=1);

namespace App\Infoboxes;

use App\WikitextConversion\TokenProcessor;

class InfoboxEntry extends AbstractInfoboxItem
{
	public static \App\Logging\Logger $logger;

	private string $key;
	private string $keyText;

	public function __construct( string $key, string $keyText )
	{
		$this->key = $key;
		$this->keyText = $keyText;
	}

	public function getHtml( array $args ): ?string
	{
		$entryValueTokens = InfoboxUtilities::getEntryValueForKey( $args, $this->key, 'is_array' );
		if ( $entryValueTokens === null ) {
			return null;
		}

		self::$logger->info( 'Placemarker' );
		self::$logger->info( print_r( $entryValueTokens, true ) );
		$entryValueHtml = (new TokenProcessor())->process( $entryValueTokens, 'inline' );

		$html = '';
		$html .= '<div class="infobox-entry">';
		$html .=     '<h3 class="infobox-entry-key">' . $this->keyText . '</h3>';
		$html .=     '<div class="infobox-entry-value">' . $entryValueHtml . '</div>';
		$html .= '</div>';

		return $html;
	}

	public function getTypeString(): string
	{
		return 'Entry';
	}

	public function getData(): array
	{
		return [
			'key' => $this->key,
			'key-text' => $this->keyText
		];
	}

	public function isContent(): bool
	{
		return true;
	}
}
