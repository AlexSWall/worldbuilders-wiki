<?php declare( strict_types = 1 );

namespace App\Models;

class UserPermissions extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */
	
	protected static function getTableName(): string
	{
		return 'UserPermissions';
	}
	
	protected static function getPrimaryKey(): string
	{
		return 'Id';
	}
	
	protected static function getDefaults(): array
	{
		return [
			'IsAdmin' => false
		];
	}


	/* == Instance Variables == */


	/* == Creators & Retrievers == */

	public static function createDefaultUserPermissions(int $userId): ?UserPermissions
	{
	    return self::createModelWithEntries(['UserId' => $userId]);
	}

	public static function retrieveUserPermissionsByUserId(int $userId): ?UserPermissions
	{
		return self::retrieveModelWithEntries(['UserId' => $userId]);
	}


	/* == Getters & Setters == */

	private function hasPermission(string $permission): bool
	{
		return (bool) $this->get($permission);
	}

	public function getUserId(): int
	{
		return $this->get('UserId');
	}

	public function isAdmin(): bool
	{
		return $this->hasPermission('IsAdmin');
	}
}
