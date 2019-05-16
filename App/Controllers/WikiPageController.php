<?php

namespace App\Controllers;

use App\Helpers\FrontEndDataUtils;

class WikiPageController extends Controller
{
	static $logger;

	private static function getData($webpageName, $webpageTitle)
	{
		return array_merge(
			FrontEndDataUtils::getWebpageDataFor($webpageName, $webpageTitle),
			FrontEndDataUtils::getFormData()
		);
	}

	public function getAddWebpageData()
	{
		return self::getData('Special:Add_Wiki_Page', 'Add Wiki Page');
	}

	public function postAddWebpage($request, $response)
	{
		return $response;
	}

	public function getEditWebpageData()
	{
		return self::getData('Special:Edit_Wiki_Page', 'Edit Wiki Page');
	}

	public function postEditWebpage($request, $response)
	{
		return $response;
	}

	public function getDeleteWebpageData()
	{
		return self::getData('Special:Delete_Wiki_Page', 'Delete Wiki Page');
	}

	public function postDeleteWebpage($request, $response)
	{
		return $response;
	}
}
