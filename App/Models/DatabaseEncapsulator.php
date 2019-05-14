<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;

abstract class DatabaseEncapsulator
{
	static $db_logger;

	private $model;
	
	protected static abstract function getTableName();
	protected static abstract function getDefaults();
	protected static abstract function getPrimaryKey();

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
			if ( !isset($value) )
				$value = null;
		unset($value);

		return array_replace( static::getDefaults(), $values );
	}
	
	protected static function createModelWithEntries($entries)
	{
		$success = self::getTable()->insert(self::createEntryWithDefaults($entries));
		if ( !$success )
			return null;

		return self::retrieveModelWithEntries($entries);
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
		$this->model->$key = $value;
		$this->update([
			$key => $value
		]);
	}

	protected function update($arr)
	{
		$keyArr = [static::getPrimaryKey() => $this->model->{static::getPrimaryKey()}];
		static::getTable()->where($keyArr)->update($arr);
	}
}