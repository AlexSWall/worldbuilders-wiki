<?php

namespace App\Models;

use App\Models\SpecialisedQueries\CharacterPermissionsQueries;

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

	private function setPermissionsIfNeeded()
	{
		if ( !$this->permissions )
	    	$this->permissions = CharacterPermissionsQueries::runCharacterIdToPermissionNamesQuery($this->getCharacterId());
	}

	public function getPermissions()
	{
		$this->setPermissionsIfNeeded();
	    return $this->permissions;
	}

	public function addPermissions($permissions)
	{
		$this->setPermissionsIfNeeded();
		for ( $permissions as $permission )
			$this->permissions->add($permission);
		
	}

	public function removePermissions($permissions)
	{
		$this->setPermissionsIfNeeded();
		for ( $permissions as $permission )
			$this->permissions->remove($permission);
		
	}

	public function hasPermissions($permissions)
	{
		$this->setPermissionsIfNeeded();
		return $this->permissions->has($permission);
	}

	public function addPermission($permission)
	{
		$this->addPermissions([ $permission ]);
	}

	public function removePermission($permission)
	{
		$this->removePermissions([ $permission ]);
	}

	public function hasPermission($permission)
	{
		$this->hasPermissions([ $permission ]);
	}

}