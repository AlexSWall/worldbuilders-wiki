<?php declare( strict_types = 1 );

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;
use App\Permissions\WikiPagePermissionBlock;

class WikiPagePermissionBlockQueries
{

	public static function getWikiPagePermissionBlocks( $wikiPageId ): array
	{
		$queryResults = DB::table('WikiPagePermissionBlocks')
				->select(['BlockPosition', 'PermissionsExpression', 'Html'])
				->where('WikiPageId', $wikiPageId)
				->get()->all();

		$wikiPagePermissionBlocksArray = array();
		foreach( $queryResults as $queryResult )
			$wikiPagePermissionBlocksArray[$queryResult->BlockPosition] = new WikiPagePermissionBlock(
				$queryResult->PermissionsExpression,
				$queryResult->Html
			);

		return $wikiPagePermissionBlocksArray;
	}

	public static function setPermissionBlocksForWikiPage( int $wikiPageId, array $blockArray ): void
	{
		self::clearPermissionBlocksForWikiPage( $wikiPageId );

		if ( count($blockArray) == 0 )
			return;

		$insertion = array();
		for ( $i = 0; $i < sizeof($blockArray); $i++ )
		{
			$block = $blockArray[$i];
			$insertion[] = [
				'WikiPageId' => $wikiPageId,
				'BlockPosition' => $i,
				'PermissionsExpression' => $block->getPermissionsExpression(),
				'Html' => $block->getHtml()
			];
		}

		DB::table('WikiPagePermissionBlocks')->insert($insertion);
	}

	public static function clearPermissionBlocksForWikiPage( int $wikiPageId ): void
	{
		DB::table('WikiPagePermissionBlocks')
				->where('WikiPageId', $wikiPageId)
				->delete();
	}
}