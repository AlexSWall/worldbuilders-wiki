<?php

declare(strict_types=1);

namespace App\Models;

class UserDetails extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'UserDetails';
	}

	protected static function getPrimaryKey(): string
	{
		return 'Id';
	}

	protected static function getDefaults(): array
	{
		return [
			'PreferredName' => null,
			'Description' => null
		];
	}


	/* == Instance Variables == */


	/* == Creators & Retrievers == */

	public static function createUserDetails( int $userId, string $preferredName ): ?UserDetails
	{
	    return self::createModelWithEntries( [
			'UserId' => $userId,
			'PreferredName' => $preferredName
		] );
	}

	public static function retrieveUserDetailsByUserId( int $userId ): ?UserDetails
	{
		return self::retrieveModelWithEntries( ['UserId' => $userId] );
	}


	/* == Getters & Setters == */

	public function getUserId(): int
	{
		return $this->get( 'UserId' );
	}

	public function getPreferredName(): string
	{
		return $this->get( 'PreferredName' );
	}

	public function getDescription(): string
	{
		return $this->get( 'Description' );
	}
}
