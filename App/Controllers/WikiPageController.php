<?php

namespace App\Controllers;

use App\Models\Webpage;
use App\Helpers\FrontendUtils;

class WikiPageController extends Controller
{
	static $logger;

	public function getAddWebpageData()
	{
		return FrontendUtils::convertToSpecialWebpageDataWithoutHTML('Special:Add_Wiki_Page', 'Add Wiki Page');
	}

	public function postAddWebpage($request, $response)
	{
		return $response;
	}

	public function getEditWebpageData()
	{
		return FrontendUtils::convertToSpecialWebpageDataWithoutHTML('Special:Edit_Wiki_Page', 'Edit Wiki Page');
	}

	public function postEditWebpage($request, $response)
	{
		return $response;
	}

	public function getDeleteWebpageData()
	{
		return FrontendUtils::convertToSpecialWebpageDataWithoutHTML('Special:Delete_Wiki_Page', 'Delete Wiki Page');
	}

	public function postDeleteWebpage($request, $response)
	{
		return $response;
	}
}
