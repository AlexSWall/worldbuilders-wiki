<?php

namespace App\Models;

use \App\Models\SpecialisedQueries\InfoboxQueries;

class Infobox extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName()
	{
		return 'Infoboxes';
	}

	protected static function getPrimaryKey()
	{
		return 'InfoboxId';
	}

	protected static function getDefaults()
	{
		return [
			'RawText' => 'infobox {}'
		];
	}


	/* == Instance Variables == */

	private ?array $infoboxItems = null;


	/* == Creators, Retrievers & Deleter == */

	public static function createInfobox($name, $rawText)
	{
	    return self::createModelWithEntries([
			'Name' => $name,
			'RawText' => $rawText
		]);
	}

	public static function retrieveInfoboxByName($name): object
	{
		return self::retrieveModelWithEntries(['Name' => $name]);
	}

	public function delete()
	{
		parent::delete();
	}


	/* == Getters & Setters == */

	public function getInfoboxId()
	{
		return $this->get('InfoboxId');
	}

	public function getName()
	{
		return $this->get('Name');
	}

	public function setName($name)
	{
		$this->set('Name', $name);
	}

	public function getRawText()
	{
		return $this->get('RawText');
	}

	public function setRawText($rawText)
	{
		$this->set('RawText', $rawText);
	}

	public function getInfoboxItems(): array
	{
		// Check whether cache private member variable is already populated.
		if ( !$this->infoboxItems )
			// If not, populate it.
			$this->setInfoboxItems(
				InfoboxQueries::getInfoboxItems( $this->getInfoboxId() )
			);

		// Return it
		return $this->infoboxItems;
	}

	public function setInfoboxItems( $infoboxItems )
	{
		// Update cache private member variable.
		$this->infoboxItems = $infoboxItems;

		// Update the database.
		InfoboxQueries::setInfoboxItems( $this->getInfoboxId(), $infoboxItems );
	}

	/* == Infobox Queries == */
}
