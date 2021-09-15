<?php declare( strict_types = 1 );

namespace App\Infoboxes;

class InfoboxSubheading extends AbstractInfoboxItem
{
	private string $subheadingText;

	public function __construct(string $subheadingText)
	{
		$this->subheadingText = $subheadingText;
	}

	public function getHtml(array $args): string
	{
		$html = '';
		$html .= '<h2 class="infobox-subheading">';
		$html .=     $this->subheadingText;
		$html .= '</h2>';

		return $html;
	}

	public function getTypeString(): string
	{
		return 'Subheading';
	}

	public function getData(): array
	{
		return [
			'key' => null,
			'subheading-text' => $this->subheadingText
		];
	}

	public function isContent(): bool
	{
		return false;
	}
}
