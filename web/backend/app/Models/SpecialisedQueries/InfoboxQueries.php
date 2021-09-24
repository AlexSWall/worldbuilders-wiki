<?php

declare(strict_types=1);

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;

use App\Infoboxes\InfoboxCaption;
use App\Infoboxes\InfoboxEntry;
use App\Infoboxes\InfoboxHorizontalRule;
use App\Infoboxes\InfoboxImage;
use App\Infoboxes\InfoboxSubheading;

class InfoboxQueries
{
	public static \App\Logging\Logger $logger;

	/**
	 * Returns an array containing AbstractInfoboxItem objects for the infobox
	 * specified, in order of position (ordered from 1 to N without gaps).
	 */
	public static function getInfoboxItems( string|int $infoboxId ): array
	{
		$result = DB::table( 'InfoboxItems AS e' )
				->select( ['e.Position', 'e.ItemKey', 'eTypes.TypeString', 'eData.DataName', 'eData.DataValue'] )
				->join( 'InfoboxItemTypes AS eTypes', 'e.InfoboxItemTypeId', '=', 'eTypes.InfoboxItemTypeId' )
				->leftJoin( 'InfoboxItemData AS eData', 'e.InfoboxItemId', '=', 'eData.infoboxItemId' )
				->where( 'e.InfoboxId', $infoboxId )
				->orderBy( 'e.Position', 'asc' )
				->get()->groupBy( 'Position' )->all();

		$infoboxItemsArray = array();

		// We ordered and then grouped by position, so loop over these.
		// TODO: Ensure assumption holds; create exception if false
		foreach ( $result as $position => $groupedRows )
		{
			// Each query result row here has the same position, item key, and type
			// string, but different data names and values. We can therefore pick
			// the first result row arbitrarily to extract the former list of data.

			$firstRow = $groupedRows[0];

			$itemKey = $firstRow->ItemKey;
			$itemTypeString = $firstRow->TypeString;

			$args = [];

			foreach ( $groupedRows as $row )
			{
				$name = $row->DataName;
				if ( $name === null )
				{
					continue;
				}
				$value = $row->DataValue;
				$args[$name] = $value;
			}

			// Switch statement to set infoboxItem.
			$infoboxItem = null;
			switch ( $itemTypeString )
			{
				case 'Caption':
					$infoboxItem = new InfoboxCaption( $itemKey );
					break;

				case 'Entry':
					if ( !array_key_exists( 'key-text', $args ) )
					{
						// TODO
						return null;
					}
					$keyText = $args['key-text'];

					$infoboxItem = new InfoboxEntry( $itemKey, $keyText );
					break;

				case 'HorizontalRule':
					$infoboxItem = new InfoboxHorizontalRule();
					break;

				case 'Image':
					$infoboxItem = new InfoboxImage( $itemKey );
					break;

				case 'Subheading':
					if ( !array_key_exists( 'subheading-text', $args ) )
					{
						// TODO
						return null;
					}
					$subheadingText = $args['subheading-text'];

					$infoboxItem = new InfoboxSubheading( $subheadingText );
					break;
			}
			$infoboxItemsArray[$position] = $infoboxItem;
		}

		return $infoboxItemsArray;
	}

	/**
	 * Enter the infobox data contained by an array of AbstractInfoboxItem
	 * objects into database
	 */
	public static function setInfoboxItems( string|int $infoboxId, array $infoboxItems ): void
	{
		self::deleteInfoboxItems( $infoboxId );

		$typeIdLookupArray = self::getTypeIdLookupArray();

		$infoboxItemDataTableValues = array();

		// Position starts at 1 as title goes at 0, so set as 0 and increment
		// immediately.
		$position = 0;
		foreach ( $infoboxItems as $infoboxItem )
		{
			$position += 1;

			$infoboxItemTypeId = $typeIdLookupArray[$infoboxItem->getTypeString()];

			$data = $infoboxItem->getData();
			$itemKey = $data['key'];
			unset( $data['key'] );

			// Insert now to get the InfoboxItemId value for the item data table
			// insertion... :(
			$infoboxItemId = DB::table( 'InfoboxItems' )->insertGetId( [
				'InfoboxId' => $infoboxId,
				'InfoboxItemTypeId' => $infoboxItemTypeId,
				'Position' => $position,
				'ItemKey' => $itemKey
			] );

			foreach ( $data as $dataName => $dataValue )
			{
				$infoboxItemDataTableValues[] = [
					'InfoboxId' => $infoboxId,
					'InfoboxItemId' => $infoboxItemId,
					'InfoboxItemTypeId' => $infoboxItemTypeId,
					'DataName' => $dataName,
					'DataValue' => $dataValue
				];
			}
		}

		DB::table( 'InfoboxItemData' )->insert( $infoboxItemDataTableValues );
	}

	public static function deleteInfoboxItems( string|int $infoboxId ): void
	{
		DB::table( 'InfoboxItems' )
				->where( 'InfoboxId', $infoboxId )
				->delete();
	}

	public static function getInfoboxNames(): array
	{
		return DB::table( 'Infoboxes' )
			->select( [ 'Name' ] )
			->orderBy( 'Name', 'asc' )
			->get()->all();
	}


	/* == Private Helper Functions == */

	/**
	 * This function obtains an array of the form [ typeString => typeId ]
	 */
	private static function getTypeIdLookupArray(): array
	{
		$result = DB::table( 'InfoboxItemTypes' )
				->select( ['InfoboxItemTypeId', 'TypeString'] )
				->get()->all();

		$typeStringToTypeId = array();

		foreach ( $result as $entry )
		{
			$infoboxItemTypeId = $entry->InfoboxItemTypeId;
			$typeString = $entry->TypeString;

			$typeStringToTypeId[$typeString] = $infoboxItemTypeId;
		}

		return $typeStringToTypeId;
	}
}
