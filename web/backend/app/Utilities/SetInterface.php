<?php declare( strict_types = 1 );

namespace App\Utilities;

interface SetInterface extends \Traversable, \Countable
{

	public function has(mixed $item): bool;

	public function add(mixed $item): void;

	public function addAll(Iterable $items): void;

	public function delete(mixed $item): void;

	public function deleteAll(Iterable $items): void;

	public function clear(): void;

	public function values(): array;
	
	public function getIterator(): \Iterator;

	public function count(): int;
}
