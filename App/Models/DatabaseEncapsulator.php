<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;

abstract class DatabaseEncapsulator
{
	static $db_logger;

	private $model;
	
	protected static abstract function getTableName();
	protected static abstract function getDefaults();

	private function __construct($model)
	{
		$this->model = $model;
	}

	private static function getTable()
	{
		self::$db_logger->addInfo('Getting a connection to the ' . static::getTableName() . ' table.');
		return DB::table(static::getTableName());
	}

	private static function createIfNotNull($model)
	{
		return $model != null ? new static($model) : null;
	}

	private static function createEntryWithDefaults($values)
	{
		foreach ($values as &$value)
			if ( empty($value) )
				$value = null;
		unset($value);

		return array_replace( static::getDefaults(), $values );
	}
	
	protected static function createModelWithEntries($entries)
	{
		$model = self::getTable()->insert(self::createEntryWithDefaults($entries));
		return self::createIfNotNull($model);
	}

	protected static function retrieveModelWithEntries($args)
	{
		return self::createIfNotNull(self::getTable()->where($args)->first());
	}

	protected function get($key)
	{
		return $this->model->$key;
	}

	protected function set($key, $value)
	{
		$this->model->update([
			$key => $value
		]);
	}

	protected function update($arr)
	{
		$this->model->update($arr);
	}
}