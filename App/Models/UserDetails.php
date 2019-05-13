<?php

namespace App\Models;

class UserDetails extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
			'PreferredName' => null,
			'Description' => null
		];
	}
	
	protected static function getTableName()
	{
		return 'user_details';
	}
	
	protected static function getPrimaryKey()
	{
		return 'Id';
	}

	/* == Creators & Retrievers == */

	public static function createUserDetails($userId, $preferredName)
	{
	    return self::createModelWithEntries([
			'UserId' => $userId,
			'PreferredName' => $preferredName
		]);
	}

	public static function retrieveUserDetailsByUserId($userId)
	{
		return self::retrieveModelWithEntries(['UserId' => $userId]);
	}


	/* == Getters & Setters == */

	public function getUserId()
	{
		return $this->get('UserId');
	}

	public function getPreferredName()
	{
		return $this->get('PreferredName');
	}

	public function getDescription()
	{
		return $this->get('Description');
	}
}