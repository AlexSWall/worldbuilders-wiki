<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\SpecialisedQueries\WikiPageQueries;
use App\Models\WikiPage;

class WikiUtilities
{
	static $logger;

	public static function recompileAllWikiPages(): void
	{
		$ids = WikiPageQueries::getAllWikiPageIds();

		self::$logger->info('Recompiling each wikipage');

		foreach ( $ids as $id )
		{
			$wikipage = WikiPage::retrieveWikiPageById($id);
			$wikipage->updateWikiPage( $wikipage->getTitle(), $wikipage->getWikiText() );
		}
	}
}
