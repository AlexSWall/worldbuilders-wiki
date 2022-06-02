<?php

declare(strict_types=1);

namespace App\Models;

class QuickNavigationSet extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'QuickNavigationSets';
	}

	protected static function getPrimaryKey(): string
	{
		return 'QuickNavigationSetId';
	}

	protected static function getDefaults(): array
	{
		return [
			'Json' => '{}'
		];
	}


	/* == Creators, Retrievers & Deleter == */

	public static function createQuickNavigationSet( int $userId, string $json ): ?QuickNavigationSet
	{
	    return self::createModelWithEntries( [
			'UserId' => $userId,
			'Json' => $json
		] );
	}

	public static function retrieveQuickNavigationSetByUserId( int $userId ): ?QuickNavigationSet
	{
		return self::retrieveModelWithEntries( ['UserId' => $userId] );
	}

	public function delete(): void
	{
		parent::delete();
	}


	/* == Getters & Setters == */

	public function getQuickNavigationSetId(): int
	{
		return $this->get( 'QuickNavigationSetId' );
	}

	public function getUserId(): int
	{
		return $this->get( 'UserId' );
	}

	public function getJson(): string
	{
		return $this->get( 'Json' );
	}

	public function setJson( string $json ): void
	{
		$this->set( 'Json', $json );
	}
}
