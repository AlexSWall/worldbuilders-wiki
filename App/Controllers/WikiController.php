<?php

namespace App\Controllers;

use App\Models\Webpage;
use App\Helpers\FrontEndDataUtils;

class WikiController extends Controller
{
	/* Request with URL of the form  '<BaseURL>/#{pageName}'  */
	public function serveWikiApp($request, $response)
	{
		return FrontEndDataUtils::getEntryPointResponse(	$this->view, $response, 'wiki' );
	}

	/* Request with URL of the form  '<BaseURL>/w/{pageName}'  */
	public function serveWikiContentGetRequest($request, $response, $args)
	{
		$pageName = $args['pageName'];

		if ( substr($pageName, 0, strlen('Special:')) === 'Special:' )
			$data = $this->getSpecialContentData($pageName);
		else 
			$data = self::getDatabaseWebpageData($pageName);

		$response = $response->withJSON([
			'webpage' => $data
		], 200, JSON_UNESCAPED_UNICODE);

		return $response;
	}

	private static function getDatabaseWebpageData($pageName)
	{
		$webpage = Webpage::retrieveWebpageByName($pageName);

		if ( is_null($webpage) )
			$webpage = Webpage::retrieveWebpageByName('Page_Not_Found');

		$webpage->renderWebpageTemplateToHTML();

		return [
			'name' => $webpage->getWebpageName(),
			'title' => $webpage->getWebpageTitle(),
			'HTML' => $webpage->getWebpageHTML()
		];
	}

	private function getSpecialContentData($pageName)
	{
		switch (substr($pageName, strlen('Special:')))
		{
			case 'Add_Wiki_Page':
				return $this->WikiPageController->getAddWebpageData();
			case 'Edit_Wiki_Page':
				return $this->WikiPageController->getEditWebpageData();
			case 'Delete_Wiki_Page':
				return $this->WikiPageController->getDeleteWebpageData();
			case 'Template_Formatting':
				return FrontEndDataUtils::getWebpageDataFor($pageName, 'Meta: Template Formatting', 
					$this->view, 'templateformatting');
			default:
				return $this->getDatabaseWebpageData('Page_Not_Found');
		}
	}
}
