<?php

namespace App\Utilities;

final class ArrayBasedSet implements \Iterator, SetInterface
{
	private $data;

	public function __construct( array $arr = [] )
	{
		foreach ($arr as $item) {
			$this->data[$item] = self::CONTAINED;
		}
	}


	/* == Set == */

	private const CONTAINED = TRUE;

	public function has($item)
	{
		return isset($this->data[$item]);
	}

	public function add($item)
	{
		$this->data[$item] = self::CONTAINED;
	}

	public function addAll($items)
	{
		foreach ( $items as $item )
			$this->data[$item] = self::CONTAINED;
	}

	public function delete($item)
	{
		unset($this->data[$item]);
	}

	public function deleteAll($items)
	{
		foreach ( $items as $item )
			unset($this->data[$item]);
	}

	public function clear()
	{
		$this->data = [];
	}

	public function values()
	{
		return array_keys($this->data);
	}


	/* == Iterator == */

	private $values;
	private $position;

	public function getIterator()
	{
		return $this;
	}

	public function rewind()
	{
		$this->values = $this->values();
		$this->position = 0;
	}

	public function current()
	{
		return $this->values[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++ $this->position;
	}

	public function valid()
	{
		return isset($this->array[$this->position]);
	}


	/* == Countable == */

	public function count()
	{
		return @count($this->data);
	}
}