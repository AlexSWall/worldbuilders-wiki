<?php

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;
use App\Models\WikiPage;

class WikiPageController extends Controller
{
	static $logger;

	public static function getWikitext($path)
	{
		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		if ($wikiPage === null)
		{
			return null;
		}

		return $wikiPage->getWikiText();
	}

	public static function createWikiPage($path, $title)
	{
		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);
		if (!is_null($wikiPage))
		{
			return 'page already exists';
		}

		$wikiPage = WikiPage::createWikiPage($path, $title);

		if (is_null($wikiPage))
		{
			return 'failed to insert into database';
		}

		return 'success';
	}

	public static function modifyWikiPage($path, $title, $contents)
	{
		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);
		if (is_null($wikiPage))
		{
			return "page doesn't exist";
		}

		$wikiPage->setTitle($title);
		$wikiPage->setWikiText($contents);

		return 'success';
	}

	public static function deleteWikiPage($path)
	{
		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);
		if (is_null($wikiPage))
		{
			return "page doesn't exist";
		}

		$wikiPage->delete();

		return 'success';
	}
}
