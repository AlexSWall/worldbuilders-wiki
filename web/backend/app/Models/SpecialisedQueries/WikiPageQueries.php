<?php

declare(strict_types=1);

namespace App\Models\SpecialisedQueries;

use Illuminate\Database\Capsule\Manager as DB;

class WikiPageQueries
{
	public static \App\Logging\Logger $logger;

	public static function getAllWikiPageIds(): array
	{
		self::$logger->info('Retrieving all wikipage IDs');

		$queryResults = DB::table( 'WikiPages' )
				->select( ['WikiPageId'] )
				->get()->pluck('WikiPageId')->all();

		return $queryResults;
	}
}
