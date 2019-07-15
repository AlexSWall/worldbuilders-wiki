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

	public function getAddWikiPageData()
	{
		return self::getData('Special:Add_Wiki_Page', 'Add Wiki Page');
	}

	public function postAddWikiPage($request, $response)
	{
		return $response;
	}

	public function getEditWikiPageData()
	{
		return self::getData('Special:Edit_Wiki_Page', 'Edit Wiki Page');
	}

	public function postEditWikiPage($request, $response)
	{
		return $response;
	}

	public function getDeleteWikiPageData()
	{
		return self::getData('Special:Delete_Wiki_Page', 'Delete Wiki Page');
	}

	public function postDeleteWikiPage($request, $response)
	{
		return $response;
	}
}
