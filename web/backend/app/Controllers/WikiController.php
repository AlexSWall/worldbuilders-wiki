<?php

namespace App\Controllers;

use App\Models\WikiPage;
use App\Helpers\FrontEndDataUtilities;
use App\Helpers\DataUtilities;

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
			case 'Template_Formatting':
				return FrontEndDataUtilities::getWikiPageDataFor($pageName, 'Meta: Template Formatting', 
					$this->view, 'templateformatting');
			default:
				return $this->getDatabaseWikiPageData('Page_Not_Found', null);
		}
	}

	private function modifyWikiContentPostRequest($request, $response, $args)
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$pagePath = trim($parsedBody['page_path']);
		$dataJSON = $parsedBody['data'];

		// -- Anonymous functions --

		$result = function($reason) use ($response)
		{
			return $response->withJSON([
				'result' => $reason
			]);
		};

		// -- Validate --

		if (!DataUtilities::is_non_empty_string($action) || !DataUtilities::is_non_empty_string($pagePath))
			return $result("'action' and 'page_name' must be non-empty strings");

		$data = DataUtilities::decodeJSONArray($dataJSON);
		if (is_null($data))
			return $result("'data' must be a JSON object/array");

		// -- Act --
		switch ($action)
		{
			case 'create':
				$title = $data['title'];
				if (!is_string($title))
					return $result("'create' action needs data with 'title' key and string value");

				return $result(WikiPageController::createWikiPage($pagePath, $title));

			case 'modify':
				// Sets $title and $content
				foreach (['title', 'content'] as $key)
				{
					$$key = $data[$key];
					if (!is_string($$key))
						return $result("'modify' action needs data with '" . $key . "' key and string value");
				}

				return $result(WikiPageController::modifyWikiPage($pagePath, $title, $content));

			case 'delete':
				return $result(WikiPageController::deleteWikiPage($pagePath));

			default:
				return $result("action must be one of 'create', 'modify', or 'delete'");
		}

		// This shouldn't happen
		return $response->withJSON([
			'result' => null
		]);
	}
}
