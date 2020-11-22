<?php

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;

class WikiPageController extends Controller
{
	static $logger;

	private static function getData($urlPath, $wikiPageTitle)
	{
		return array_merge(
			FrontEndDataUtilities::getWikiPageDataFor($urlPath, $wikiPageTitle),
			FrontEndDataUtilities::getFormData()
		);
	}

	public function createWikiPage($path, $title)
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

	public function modifyWikiPage($path, $title, $contents)
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

	public function deleteWikiPage($path)
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
