<?php

namespace App\Controllers;

use App\Models\Webpage;
use App\Helpers\FrontendUtils;

class WikiController extends Controller
{
	/* Request of form <URL>/#{pageName} */
	public function serveWikiApp($request, $response)
	{
		return $this->view->render($response, 'Indexes/wiki.index.twig',
			FrontendUtils::constructFrontendParametersArray());
	}

	/* Request of <URL>/w/{pageName} */
	public function serveWikiContentGetRequest($request, $response, $args)
	{
		$pageName = $args['pageName'];

		if ( substr($pageName, 0, strlen('Special:')) === 'Special:' )
			$data = $this->getSpecialContentData($pageName);
		else 
			$data = self::getDatabaseWebpageData($pageName);

		$response = $response->withJSON($data, 200, JSON_UNESCAPED_UNICODE);

		return $response;
	}

	private static function getDatabaseWebpageData($pageName = 'Page_Not_Found')
	{
		$webpage = Webpage::retrieveWebpageByName($pageName);

		if ( is_null($webpage) )
			$webpage = Webpage::retrieveWebpageByName('Page_Not_Found');

		$webpage->renderWebpageTemplateToHTML();

		return [
			'webpage' => [
				'name' => $webpage->getWebpageName(),
				'title' => $webpage->getWebpageTitle(),
				'HTML' => $webpage->getWebpageHTML()
			]
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
				return FrontendUtils::convertToSpecialWebpageDataWithHTML($pageName, 'Meta: Template Formatting', 
					$this->view, 'templateformatting');
			default:
				return $this->getDatabaseWebpageData();
		}
	}
}
