<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\SpecialisedQueries\CharacterPermissionsQueries;

use App\Utilities\ArrayBasedSet;

class Character extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'Characters';
	}

	protected static function getPrimaryKey(): string
	{
		return 'CharacterId';
	}

	protected static function getDefaults(): array
	{
		return [
		];
	}


	/* == Instance Variables == */

	private ?ArrayBasedSet $permissions = null;


	/* == Creators & Retrievers == */

	public static function createCharacter( int $UserId, string $fullName ): ?Character
	{
	    return self::createModelWithEntries( [
			'UserId' => $UserId,
			'FullName' => $fullName
		] );
	}

	public static function retrieveCharacterByCharacterId( int $characterId ): ?Character
	{
		return self::retrieveModelWithEntries( ['CharacterId' => $characterId] );
	}

	public static function retrieveCharactersByUserId( int $userId ): array
	{
		return self::retrieveModelsWithEntries( ['UserId' => $userId] );
	}


	/* == Getters & Setters == */

	public function getCharacterId(): int
	{
		return $this->get( 'CharacterId' );
	}

	public function getUserId(): int
	{
		return $this->get( 'UserId' );
	}


	/* == Character Details == */

	public function createCharacterDetails( string $fullName ): void
	{
		CharacterDetails::createCharacterDetails( $this->getCharacterId(), $fullName );
	}

	public function getCharacterDetails(): CharacterDetails
	{
	    /* Lazy instantiation. */
		if ( !$this->CharacterDetails )
		{
		    $this->CharacterDetails = CharacterDetails::retrieveCharacterDetailsByCharacterId( $this->getCharacterId() );
		}
		return $this->CharacterDetails;
	}


	/* == Character Permissions == */

	private function setPermissionsFieldIfNeeded(): void
	{
		if ( !$this->permissions )
		{
			$this->permissions = CharacterPermissionsQueries::getCharacterPermissions( $this->getCharacterId() );
		}
	}

	public function getPermissions(): ArrayBasedSet
	{
		$this->setPermissionsFieldIfNeeded();
		return $this->permissions;
	}

	public function addPermissions( array $permissions ): void
	{
		$this->setPermissionsFieldIfNeeded();
		$this->permissions->addAll( $permissions );
		CharacterPermissionsQueries::addCharacterPermissions( $this->getCharacterId(), $permissions );
	}

	public function removePermissions( array $permissions ): void
	{
		$this->setPermissionsFieldIfNeeded();
		$this->permissions->removeAll( $permissions );
		CharacterPermissionsQueries::removeCharacterPermissions( $this->getCharacterId(), $permissions );
	}

	public function hasPermissions( array $permissions ): bool
	{
		$this->setPermissionsFieldIfNeeded();
		return $this->permissions->has( $permissions );
	}

	public function addPermission( string $permission ): void
	{
		$this->addPermissions( [ $permission ] );
	}

	public function removePermission( string $permission ): void
	{
		$this->removePermissions( [ $permission ] );
	}

	public function hasPermission( string $permission ): bool
	{
		return $this->hasPermissions( [ $permission ] );
	}
}
