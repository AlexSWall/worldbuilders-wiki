<?php

declare(strict_types=1);

namespace App\Infoboxes;

class InfoboxImage extends AbstractInfoboxItem
{
	public static \App\Logging\Logger $logger;

	private string $key;

	public function __construct( string $key )
	{
		$this->key = $key;
	}

	public function getHtml( array $args ): ?string
	{
		/* // TODO: Get arguments array for InfoboxInfo */
		/* if ( !array_key_exists($this->key, $args) ) */
		/* 	return null; */
		/* $myArgs = $args[$this->key]; */

		$filenameToken = InfoboxUtilities::getEntryValueForKey( $args, $this->key, 'is_array' );
		if ( $filenameToken === null ) {
			return null;
		}

		$filename = InfoboxUtilities::unpackTextToken( $filenameToken );

		// TODO: Extract width
		$width = '270';

		// Create HTML
		$html = '';
		$html .= '<figure>';
		$html .=     "<img src=\"/images/wiki-images/{$filename}\" class=\"infobox-image\" width=\"{$width}\">";
		$html .= '</figure>';

		return $html;
	}

	public function getTypeString(): string
	{
		return 'Image';
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
