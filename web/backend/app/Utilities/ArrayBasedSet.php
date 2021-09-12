<?php declare( strict_types = 1 );

namespace App\Utilities;

final class ArrayBasedSet implements \Iterator, SetInterface
{
	private array $data;

	public function __construct( array $arr = [] )
	{
		$this->data = array();
		foreach ($arr as $item) {
			$this->data[$item] = self::CONTAINED;
		}
	}


	/* == Set == */

	private const CONTAINED = TRUE;

	public function has( mixed $item ): bool
	{
		return isset($this->data[$item]);
	}

	public function add( mixed $item ): void
	{
		$this->data[$item] = self::CONTAINED;
	}

	public function addAll( Iterable $items ): void
	{
		foreach ( $items as $item )
			$this->data[$item] = self::CONTAINED;
	}

	public function delete( mixed $item ): void
	{
		unset($this->data[$item]);
	}

	public function deleteAll( Iterable $items ): void
	{
		foreach ( $items as $item )
			unset($this->data[$item]);
	}

	public function clear(): void
	{
		$this->data = [];
	}

	public function values(): array
	{
		return array_keys($this->data);
	}


	/* == Iterator == */

	private array $values;
	private int $position;

	public function getIterator(): \Iterator
	{
		return $this;
	}

	public function rewind(): void
	{
		$this->values = $this->values();
		$this->position = 0;
	}

	public function current(): mixed
	{
		return $this->values[$this->position];
	}

	public function key(): mixed
	{
		return $this->position;
	}

	public function next(): void
	{
		++ $this->position;
	}

	public function valid(): bool
	{
		return isset($this->array[$this->position]);
	}


	/* == Countable == */

	public function count(): int
	{
		return @count($this->data);
	}
}
