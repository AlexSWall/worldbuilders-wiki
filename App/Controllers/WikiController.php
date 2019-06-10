<?php

namespace App\Controllers;

use App\Models\Webpage;
use App\Helpers\FrontEndDataUtilities;

class WikiController extends Controller
{
	/* Request with URL of the form  '<BaseURL>/#{pageName}'  */
	public function serveWikiApp($request, $response)
	{
		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'wiki' );
	}

	/* Request with URL of the form  '<BaseURL>/w/{pageName}'  */
	public function serveWikiContentGetRequest($request, $response, $args)
	{
		$pageName = $args['pageName'];

		$character = $this->auth->getCharacter();
		if ( $character )
			$viewingPermissions = $character->getPermissions();
		else
			$viewingPermissions = null;

		if ( substr($pageName, 0, strlen('Special:')) === 'Special:' )
			$data = $this->getSpecialContentData($pageName);
		else 
			$data = self::getDatabaseWebpageData($pageName, $viewingPermissions);

		$response = $response->withJSON([
			'webpage' => $data
		], 200, JSON_UNESCAPED_UNICODE);

		return $response;
	}

	private static function getDatabaseWebpageData($pageName, $viewingPermissions)
	{
		$webpage = Webpage::retrieveWebpageByUrlPath($pageName);

		if ( is_null($webpage) )
			$webpage = Webpage::retrieveWebpageByUrlPath('Page_Not_Found');

		$webpage->renderWikiTextToHtmlBlocks();

		return FrontEndDataUtilities::constructEndpointDataArray(
			$webpage->getUrlPath(),
			$webpage->getTitle(),
			$webpage->getHtmlForPermissionsExpression($viewingPermissions)
		);
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
				return FrontEndDataUtilities::getWebpageDataFor($pageName, 'Meta: Template Formatting', 
					$this->view, 'templateformatting');
			default:
				return $this->getDatabaseWebpageData('Page_Not_Found');
		}
	}
}
