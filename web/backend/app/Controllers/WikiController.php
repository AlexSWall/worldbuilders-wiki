<?php

namespace App\Controllers;

use App\Models\WikiPage;
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
			$data = self::getDatabaseWikiPageData($pageName, $viewingPermissions);

		$response = $response->withJSON([
			'wikiPage' => $data
		], 200, JSON_UNESCAPED_UNICODE);

		return $response;
	}

	private static function getDatabaseWikiPageData($pageName, $viewingPermissions)
	{
		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($pageName);

		if ( is_null($wikiPage) )
			$wikiPage = WikiPage::retrieveWikiPageByUrlPath('Page_Not_Found');

		$wikiPage->renderWikiTextToHtmlBlocks();

		return FrontEndDataUtilities::constructEndpointDataArray(
			$wikiPage->getUrlPath(),
			$wikiPage->getTitle(),
			$wikiPage->getHtmlForPermissionsExpression($viewingPermissions)
		);
	}

	private function getSpecialContentData($pageName)
	{
		switch (substr($pageName, strlen('Special:')))
		{
			case 'Add_Wiki_Page':
				return $this->WikiPageController->getAddWikiPageData();
			case 'Edit_Wiki_Page':
				return $this->WikiPageController->getEditWikiPageData();
			case 'Delete_Wiki_Page':
				return $this->WikiPageController->getDeleteWikiPageData();
			case 'Template_Formatting':
				return FrontEndDataUtilities::getWikiPageDataFor($pageName, 'Meta: Template Formatting', 
					$this->view, 'templateformatting');
			default:
				return $this->getDatabaseWikiPageData('Page_Not_Found');
		}
	}
}
