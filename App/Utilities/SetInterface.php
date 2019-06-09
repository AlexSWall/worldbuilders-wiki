<?php

namespace App\Utilities;

interface SetInterface extends \Traversable, \Countable
{

	public function has($item);

	public function add($item);

	public function addAll($items);

	public function delete($item);

	public function deleteAll($items);

	public function clear();

	public function values();
	
	public function getIterator();

	public function count();
}