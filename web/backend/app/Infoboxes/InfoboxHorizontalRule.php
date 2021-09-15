<?php declare( strict_types = 1 );

namespace App\Infoboxes;

class InfoboxHorizontalRule extends AbstractInfoboxItem
{
	public function __construct()
	{
	}

	public function getHtml(array $args): string
	{
		return '<hr>';
	}

	public function getTypeString(): string
	{
		return 'HorizontalRule';
	}

	public function getData(): array
	{
		return [
			'key' => null
		];
	}

	public function isContent(): bool
	{
		return false;
	}
}
