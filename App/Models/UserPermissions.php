<?php

namespace App\Models;

class UserPermissions extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
			'IsAdmin' => false
		];
	}
	
	protected static function getTableName()
	{
		return 'UserPermissions';
	}
	
	protected static function getPrimaryKey()
	{
		return 'Id';
	}

	/* == Creators & Retrievers == */

	public static function createDefaultUserPermissions($userId)
	{
	    return self::createModelWithEntries(['UserId' => $userId]);
	}

	public static function retrieveUserPermissionsByUserId($userId)
	{
		return self::retrieveModelWithEntries(['UserId' => $userId]);
	}


	/* == Getters & Setters == */

	private function hasPermission($permission)
	{
		return (bool) $this->get($permission);
	}

	public function getUserId()
	{
		return $this->get('UserId');
	}

	public function isAdmin()
	{
		return $this->hasPermission('IsAdmin');
	}
}
