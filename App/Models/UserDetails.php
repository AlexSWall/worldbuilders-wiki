<?php

namespace App\Models;

class UserDetails extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
			'preferred_name' => null,
			'description' => null
		];
	}
	
	protected static function getTableName()
	{
		return 'user_details';
	}

	/* == Creators & Retrievers == */

	public static function createUserDetails($userId, $preferredName)
	{
	    return self::createModelWithEntries([
			'user_id' => $userId,
			'preferred_name' => $preferredName
		]);
	}

	public static function retrieveUserDetailsByUserId($userId)
	{
		self::retrieveModelWithEntries(['user_id' => $userId]);
	}


	/* == Getters & Setters == */

	public function getUserId()
	{
		return $this->get('user_id');
	}

	public function getPreferredName()
	{
		return $this->get('preferred_name');
	}

	public function getDescription()
	{
		return $this->get('description');
	}
}