<?php declare( strict_types = 1 );

namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder as Table;

abstract class DatabaseEncapsulator
{
	static $db_logger;

	private $model;

	protected static abstract function getTableName(): string;
	protected static abstract function getPrimaryKey(): string;
	protected static abstract function getDefaults(): array;

	private function __construct($model)
	{
		$this->model = $model;
	}

	private static function getTable(): Table
	{
		self::$db_logger->info('Getting a connection to the ' . static::getTableName() . ' table.');
		$ret = DB::table(static::getTableName());
		self::$db_logger->info('Got a connection to the ' . static::getTableName() . ' table.');
		return $ret;
	}

	private static function createIfNotNull($model): ?static
	{
		return $model != null ? new static($model) : null;
	}

	private static function createEntryWithDefaults(array $values): array
	{
		foreach ($values as &$value)
			if ( !isset($value) )
				$value = null;
		unset($value);

		return array_replace( static::getDefaults(), $values );
	}

	protected static function createModelWithEntries(array $entries): ?static
	{
		$success = self::getTable()->insert(self::createEntryWithDefaults($entries));
		if ( !$success )
			return null;

		return self::retrieveModelWithEntries($entries);
	}

	protected static function retrieveModelWithEntries(array $args): ?static
	{
		self::$db_logger->info(
			'Retrieving entry from ' . static::getTableName()
			.' table with args ' . json_encode($args)
		);

		return self::createIfNotNull(self::getTable()->where($args)->first());
	}

	protected static function retrieveModelsWithEntries(array $args): array
	{
		$models = array();
		foreach ( self::getTable()->where($args)->get()->getIterator() as $bareModel )
			$models[] = self::createIfNotNull($bareModel);
		return $models;
	}

	protected function get(string $key): mixed
	{
		return $this->model->$key;
	}

	protected function set(string $key, mixed $value): void
{
		$this->model->$key = $value;
		$this->update([
			$key => $value
		]);
	}

	protected function update(array $values): void
	{
		$keyArr = [ static::getPrimaryKey() => $this->get( static::getPrimaryKey() ) ];
		static::getTable()->where($keyArr)->update($values);
	}

	protected function delete(): void
	{
		$keyArr = [ static::getPrimaryKey() => $this->get( static::getPrimaryKey() ) ];
		static::getTable()->where($keyArr)->delete();
	}
}
