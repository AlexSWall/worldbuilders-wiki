<?php declare( strict_types = 1 );

namespace App\Models;

use \App\Models\SpecialisedQueries\InfoboxQueries;

class Infobox extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'Infoboxes';
	}

	protected static function getPrimaryKey(): string
	{
		return 'InfoboxId';
	}

	protected static function getDefaults(): array
	{
		return [
			'RawText' => 'infobox {}'
		];
	}


	/* == Instance Variables == */

	private ?array $infoboxItems = null;


	/* == Creators, Retrievers & Deleter == */

	public static function createInfobox(string $name, string $rawText): ?Infobox
	{
	    return self::createModelWithEntries([
			'Name' => $name,
			'RawText' => $rawText
		]);
	}

	public static function retrieveInfoboxByName(string $name): ?Infobox
	{
		return self::retrieveModelWithEntries(['Name' => $name]);
	}

	public function delete(): void
	{
		parent::delete();
	}


	/* == Getters & Setters == */

	public function getInfoboxId(): int
	{
		return $this->get('InfoboxId');
	}

	public function getName(): string
	{
		return $this->get('Name');
	}

	public function setName(string $name): void
	{
		$this->set('Name', $name);
	}

	public function getRawText(): string
	{
		return $this->get('RawText');
	}

	public function setRawText(string $rawText): void
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

	public function setInfoboxItems( array $infoboxItems ): void
	{
		// Update cache private member variable.
		$this->infoboxItems = $infoboxItems;

		// Update the database.
		InfoboxQueries::setInfoboxItems( $this->getInfoboxId(), $infoboxItems );
	}

	/* == Infobox Queries == */
}
