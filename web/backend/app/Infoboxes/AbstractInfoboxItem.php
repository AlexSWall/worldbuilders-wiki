<?php declare( strict_types = 1 );

namespace App\Infoboxes;

abstract class AbstractInfoboxItem
{
	public abstract function getHtml(array $args): ?string;
	public abstract function getTypeString(): string;
	public abstract function getData(): array;
	public abstract function isContent(): bool;
}
