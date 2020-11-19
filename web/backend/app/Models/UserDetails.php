<?php

namespace App\Models;

class UserDetails extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */
	
	protected static function getTableName()
	{
		return 'UserDetails';
	}
	
	protected static function getPrimaryKey()
	{
		return 'Id';
	}
	
	protected static function getDefaults()
	{
		return [
			'PreferredName' => null,
			'Description' => null
		];
	}


	/* == Instance Variables == */


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