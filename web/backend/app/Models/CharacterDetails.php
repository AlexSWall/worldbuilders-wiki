<?php

namespace App\Models;

class CharacterDetails extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */
	
	protected static function getTableName()
	{
		return 'CharacterDetails';
	}
	
	protected static function getPrimaryKey()
	{
		return 'CharacterDetailsId';
	}

	protected static function getDefaults()
	{
		return [
			'Nickname' => null,
			'WikiPageLink' => null
		];
	}


	/* == Instance Variables == */


	/* == Creators & Retrievers == */

	public static function createCharacterDetails($characterId, $fullName)
	{
	    return self::createModelWithEntries([
			'CharacterId' => $characterId,
			'FullName' => $fullName
		]);
	}

	public static function retrieveCharacterDetailsByCharacterId($characterId)
	{
		return self::retrieveModelWithEntries(['CharacterId' => $characterId]);
	}


	/* == Getters & Setters == */

	public function getCharacterDetailsId()
	{
		return $this->get('CharcterDetailsId');
	}

	public function getCharacterId()
	{
		return $this->get('CharacterId');
	}

	public function getFullName()
	{
		return $this->get('FullName');
	}

	public function setFullName($fullName)
	{
		$this->set('FullName', $fullName);
	}

	public function getNickname()
	{
		return $this->get('Nickname');
	}

	public function setNickname($nickname)
	{
		$this->set('Nickname', $nickname);
	}

	public function getWikiPageLink()
	{
		return $this->get('WikiPageLink');
	}

	public function setWikiPageLink($wikiPageLink)
	{
		$this->set('WikiPageLink', $wikiPageLink);
	}

	public function getDescription()
	{
		return $this->get('Description');
	}

	public function setDescription($description)
	{
		$this->set('Description', $description);
	}
}
