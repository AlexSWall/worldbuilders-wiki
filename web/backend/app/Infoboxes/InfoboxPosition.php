<?php

declare(strict_types=1);

namespace App\Infoboxes;

class InfoboxPosition
{
	public static \App\Logging\Logger $logger;

	// [ AbstractInfoboxItem ]
	private array $items;

	public function __construct( array $items )
	{
		$this->items = $items;
	}

	public function getItemForPosition( array $args ): ?AbstractInfoboxItem
	{
		foreach ( $this->items as $item )
		{
			$html = $item->getHtml( $args );

			if ( $html === null )
			{
				continue;
			}
			else
			{
				return $item;
			}
		}

		// None of the items match
		return null;
	}

	/**
	 * $itemsArray is of type [ AbstractInfoboxItem | [ AbstractInfoboxItem ] ]
	 */
	public static function constructPositionsArray( array $itemsArray )
	{
		$positionsArray = [];

		foreach ( $itemsArray as $position => $itemOrItems )
		{
			if ( is_array( $itemOrItems ) )
			{
				$items = $itemOrItems;
				$positionsArray[$position] = new InfoboxPosition( $items );
			}
			else
			{
				$item = $itemOrItems;
				$positionsArray[$position] = new InfoboxPosition( [ $item ] );
			}
		}

		return $positionsArray;
	}
}
