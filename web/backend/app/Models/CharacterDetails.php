<?php

declare(strict_types=1);

namespace App\Models;

class CharacterDetails extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'CharacterDetails';
	}

	protected static function getPrimaryKey(): string
	{
		return 'CharacterDetailsId';
	}

	protected static function getDefaults(): array
	{
		return [
			'Nickname' => null,
			'WikiPageLink' => null
		];
	}


	/* == Instance Variables == */


	/* == Creators & Retrievers == */

	public static function createCharacterDetails( int $characterId, string $fullName ): ?CharacterDetails
	{
	    return self::createModelWithEntries( [
			'CharacterId' => $characterId,
			'FullName' => $fullName
		] );
	}

	public static function retrieveCharacterDetailsByCharacterId( int $characterId ): ?CharacterDetails
	{
		return self::retrieveModelWithEntries( ['CharacterId' => $characterId] );
	}


	/* == Getters & Setters == */

	public function getCharacterDetailsId(): int
	{
		return $this->get( 'CharcterDetailsId' );
	}

	public function getCharacterId(): int
	{
		return $this->get( 'CharacterId' );
	}

	public function getFullName(): string
	{
		return $this->get( 'FullName' );
	}

	public function setFullName( string $fullName ): void
	{
		$this->set( 'FullName', $fullName );
	}

	public function getNickname(): string
	{
		return $this->get( 'Nickname' );
	}

	public function setNickname( string $nickname ): void
	{
		$this->set( 'Nickname', $nickname );
	}

	public function getWikiPageLink(): string
	{
		return $this->get( 'WikiPageLink' );
	}

	public function setWikiPageLink( string $wikiPageLink ): void
	{
		$this->set( 'WikiPageLink', $wikiPageLink );
	}

	public function getDescription(): string
	{
		return $this->get( 'Description' );
	}

	public function setDescription( string $description ): void
	{
		$this->set( 'Description', $description );
	}
}
