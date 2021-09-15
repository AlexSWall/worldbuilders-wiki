<?php

declare(strict_types=1);

namespace App\Infoboxes;

abstract class AbstractInfoboxItem
{
	abstract public function getHtml( array $args ): ?string;
	abstract public function getTypeString(): string;
	abstract public function getData(): array;
	abstract public function isContent(): bool;
}
