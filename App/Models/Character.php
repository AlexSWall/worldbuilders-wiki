<?php

namespace App\Models;

class Character extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */
	
	protected static function getTableName()
	{
		return 'Characters';
	}
	
	protected static function getPrimaryKey()
	{
		return 'CharacterId';
	}

	protected static function getDefaults()
	{
		return [
		];
	}


	/* == Instance Variables == */

	private $permissions;


	/* == Creators & Retrievers == */

	public static function createCharacter($UserId, $fullName)
	{
	    return self::createModelWithEntries([
			'UserId' => $UserId,
			'FullName' => $fullName
		]);
	}

	public static function retrieveCharacterByUserCharacterId($characterId)
	{
		return self::retrieveModelWithEntries(['CharacterId' => $characterId]);
	}

	public static function retrieveCharactersByUserId($userId)
	{
		return self::retrieveModelsWithEntries(['UserId' => $userId]);
	}


	/* == Getters & Setters == */

	public function getCharacterId()
	{
		return $this->get('CharacterId');
	}

	public function getUserId()
	{
		return $this->get('UserId');
	}


	/* == Character Details == */

	public function createCharacterDetails($fullName)
	{
		CharacterDetails::createCharacterDetails($this->getCharacterId(), $fullName);
	}

	public function getCharacterDetails()
	{
	    /* Lazy instantiation. */
		if ( !$this->CharacterDetails )
		    $this->CharacterDetails = CharacterDetails::retrieveCharacterDetailsByCharacterId($this->getCharacterId());
		return $this->CharacterDetails;
	}

	public function getPreferredName()
	{
		return $this->getCharacterDetails()->getPreferredName();
	}


	/* == Character Permissions == */

	public function addPermission()
	{
		// TODO
	}

	public function addPermissions()
	{
		// TODO
	}

	public function removePermissio()
	{
		// TODO
	}

	public function removePermission()
	{
		// TODO
	}

	public function getPermissions()
	{
		// TODO
	}

	public function hasPermission()
	{
		// TODO
	}

}