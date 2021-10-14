<?php

declare(strict_types=1);

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;

use App\Exceptions\ServerException;
use App\Infoboxes\InfoboxCaption;
use App\Infoboxes\InfoboxEntry;
use App\Infoboxes\InfoboxHorizontalRule;
use App\Infoboxes\InfoboxImage;
use App\Infoboxes\InfoboxPosition;
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

		self::$logger->json_dump($result);

		// We ordered and then grouped by position, so loop over these.
		// TODO: Ensure assumption holds; create exception if false
		foreach ( $result as $position => $groupedRows )
		{
			self::$logger->json_dump($groupedRows);

			// Each query result row here has the same position and type string,
			// but different item keys, data names, and data values.
			// We can therefore pick the first result row arbitrarily to extract
			// the type string.
			$itemTypeString = $groupedRows[0]->TypeString;

			// Set the default item key to be the item key of the first row, in
			// case the item has no data keys to over-write it.
			$firstItemKey = $groupedRows[0]->ItemKey;

			$args = [];
			// Constructs args data of the form
			// $args = [ itemKey => [ dataName => dataValue ] ]
			foreach ( $groupedRows as $row )
			{
				// itemKey may be null; this is fine.
				$itemKey = $row->ItemKey;
				$dataName = $row->DataName;
				$dataValue = $row->DataValue;

				if ( $dataName !== null )
				{
					if ( ! in_array( $itemKey, $args, true ) )
					{
						$args[$itemKey] = [ $dataName => $dataValue ];
					}
					else
					{
						$args[$itemKey][$dataName] = $dataValue;
					}
				}
			}

			$assertNumKeys = function( $minNum = null, $maxNum = null ) use ( $itemTypeString, $args )
			{
				$numKeys = count( $args );

				$msg = null;
				if ( $minNum && $numKeys < $minNum )
				{
					$msg = "Infobox item of type {$itemTypeString} should have"
						. " at least {$minNum} entry keys, but found {$numKeys}";
				}
				elseif ( $maxNum && $numKeys > $maxNum )
				{
					$msg = "Infobox item of type {$itemTypeString} should have"
						. " at least {$minNum} entry keys, but found {$numKeys}";
				}

				if ( $msg !== null )
				{
					self::$logger->warning( $msg );
					throw	new ServerException( $msg );
				}
				else
				{
					self::$logger->info( 'Correct number of infobox item keys' );
				}
			};

			$assertDataKeyExists = function( $infoboxKeyArgs, $dataKey ) use ( $itemTypeString )
			{
				if ( !array_key_exists( $dataKey, $infoboxKeyArgs ) )
				{
					$msg = "Infobox item of type {$itemTypeString} should have a"
						. " data key of {$dataKey} but {$dataKey} not found in args.";
					self::$logger->warning( $msg );
					throw	new ServerException( $msg );
				}
			};

			// Switch statement to set infoboxItem.
			switch ( $itemTypeString )
			{
				case 'Caption':
					$assertNumKeys(0, 0);

					// Add to infobox items array
					$infoboxItemsArray[$position] = new InfoboxCaption( $firstItemKey );
					break;

				case 'Entry':
					$assertNumKeys(1, null);

					$infoboxEntries = [];
					foreach ( $args as $itemKey => $dataArray )
					{
						$assertDataKeyExists( $dataArray, 'key-text' );

						$keyText = $dataArray['key-text'];

						$infoboxEntries[] = new InfoboxEntry( $itemKey, $keyText );
					}

					// Add to infobox items array
					$infoboxItemsArray[$position] = $infoboxEntries;
					break;

				case 'HorizontalRule':
					$assertNumKeys(0, 0);

					// Add to infobox items array
					$infoboxItemsArray[$position] = new InfoboxHorizontalRule();
					break;

				case 'Image':
					$assertNumKeys(0, 0);

					// Add to infobox items array
					$infoboxItemsArray[$position] = new InfoboxImage( $firstItemKey );
					break;

				case 'Subheading':
					$assertNumKeys(1, 1);

					// Subheading has no item key
					$dataArray = $args[null];

					$assertDataKeyExists( $dataArray, 'subheading-text' );

					$subheadingText = $dataArray['subheading-text'];

					// Add to infobox items array
					$infoboxItemsArray[$position] = new InfoboxSubheading( $subheadingText );
					break;
			}
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

		self::$logger->info( 'Setting infobox items in database for infobox ID ' . $infoboxId );
		self::$logger->dump( $infoboxItems );

		// Position starts at 1 as title goes at 0, so set as 0 and increment
		// immediately.
		$position = 0;
		foreach ( $infoboxItems as $infoboxItemsForPosition )
		{
			$position += 1;

			self::$logger->dump( $infoboxItemsForPosition );

			// If the loop variable is a single infobox item, wrap it into a
			// one-element array to act as a single infobox for the position.
			if ( ! is_array( $infoboxItemsForPosition ) )
			{
				$infoboxItemsForPosition = [ $infoboxItemsForPosition ];
			}

			// For each infobox item in this position...
			foreach ( $infoboxItemsForPosition as $infoboxItem )
			{
				// Insert infobox item, get the item's ID, and then add to item data
				// table values array.
				array_push($infoboxItemDataTableValues, ...self::insertInfoboxItem(
					$infoboxId,
					$typeIdLookupArray[$infoboxItem->getTypeString()],
					$position,
					$infoboxItem->getData()
				));
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
		$namesResult = DB::table( 'Infoboxes' )
			->select( [ 'Name' ] )
			->orderBy( 'Name', 'asc' )
			->get()->all();

		// TODO: Do in a more sensible way once you have an internet connection...
		$names = [];
		foreach ( $namesResult as $nameResult )
		{
			$names[] = $nameResult->Name;
		}

		return $names;
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

	private static function insertInfoboxItem(
		int $infoboxId,
		int $infoboxItemTypeId,
		int $position,
		array $data
	): array
	{
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

		$infoboxItemDataTableValues = [];
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

		return $infoboxItemDataTableValues;
	}
}
