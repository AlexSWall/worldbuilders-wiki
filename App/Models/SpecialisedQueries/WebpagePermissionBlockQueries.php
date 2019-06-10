<?php declare( strict_types = 1 );

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\WebpagePermissionBlock;

class WebpagePermissionBlockQueries
{

	public static function getWebpagePermissionBlocks( $webpageId ): array
	{
		$queryResults = DB::table('WebpagePermissionBlocks')
				->select(['BlockPosition', 'PermissionsExpression', 'Html'])
				->where('WebpageId', $webpageId)
				->get()->all();

		$webpagePermissionBlocksArray = array();
		foreach( $queryResults as $queryResult )
			$webpagePermissionBlocksArray[$queryResult->BlockPosition] = new WebpagePermissionBlock(
				$queryResult->PermissionsExpression,
				$queryResult->Html
			);

		return $webpagePermissionBlocksArray;
	}

	public static function setPermissionBlocksForWebpage( int $webpageId, array $blockArray ): void
	{
		self::clearPermissionBlocksForWebpage( $webpageId );

		if ( count($blockArray) == 0 )
			return;

		$insertion = array();
		for ( $i = 0; $i < sizeof($blockArray); $i++ )
		{
			$block = $blockArray[$i];
			$insertion[] = [
				'WebpageId' => $webpageId,
				'BlockPosition' => $i,
				'PermissionsExpression' => $block->getPermissionsExpression(),
				'Html' => $block->getHtml()
			];
		}

		DB::table('WebpagePermissionBlocks')->insert($insertion);
	}

	public static function clearPermissionBlocksForWebpage( int $webpageId ): void
	{
		DB::table('WebpagePermissionBlocks')
				->where('WebpageId', $webpageId)
				->delete();
	}
}