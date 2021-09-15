<?php

declare(strict_types=1);

namespace App\Infoboxes;

use App\WikitextConversion\TokenProcessor;

class InfoboxCaption extends AbstractInfoboxItem
{
	private string $key;

	public function __construct( string $key )
	{
		$this->key = $key;
	}

	public function getHtml( array $args ): ?string
	{
		$captionTokens = InfoboxUtilities::getEntryValueForKey( $args, $this->key, 'is_array' );
		if ( $captionTokens === null ) {
			return null;
		}

		$captionHtml = (new TokenProcessor())->process( $captionTokens, 'inline' );

		// Create HTML
		$html = '';
		$html .= '<div class="infobox-capion">';
		$html .=     $captionHtml;
		$html .= '</div>';

		return $html;
	}

	public function getTypeString(): string
	{
		return 'Caption';
	}

	public function getData(): array
	{
		return [
			'key' => $this->key
		];
	}

	public function isContent(): bool
	{
		return true;
	}
}
