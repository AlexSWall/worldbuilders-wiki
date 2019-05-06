<?php

namespace App\Models;

class UserPermissions extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
			'is_admin' => false
		];
	}
	
	protected static function getTableName()
	{
		return 'user_permissions';
	}

	/* == Creators & Retrievers == */

	public static function createDefaultUserPermissions($userId)
	{
	    return self::createModelWithEntries(['user_id' => $userId]);
	}

	public static function retrieveUserPermissionsByUserId($userId)
	{
		self::retrieveModelWithEntries(['user_id' => $userId]);
	}


	/* == Getters & Setters == */

	private function hasPermission($permission)
	{
		return (bool) $this->get($permission);
	}

	public function getUserId()
	{
		return $this->get('user_id');
	}

	public function isAdmin()
	{
		return $this->hasPermission('is_admin');
	}
}
