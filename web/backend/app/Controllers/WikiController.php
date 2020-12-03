<?php

namespace App\Controllers;

use App\Models\WikiPage;
use App\Helpers\FrontEndDataUtilities;
use App\Helpers\DataUtilities;

class WikiController extends Controller
{
	static $logger;

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

	public function getWikitext($request, $response, $args)
	{
		$pagePath = $request->getQueryParam('wikipage');
		self::$logger->addInfo('Received request for wikitext of page ' . $pagePath);

		$wikitext = ($pagePath === null)
			? null
			: WikiPageController::getWikiText($pagePath);

			return $response->withJSON([
				'wikitext' => $wikitext
			], 200, \JSON_UNESCAPED_UNICODE);
	}

	public function modifyWikiContentPostRequest($request, $response)
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$pagePath = trim($parsedBody['page_path']);
		$data = $parsedBody['data'];

		self::$logger->addInfo('Received post request to modify wiki content for ' . $pagePath);
		self::$logger->addInfo('Action: ' . $action . '; Page Path: ' . $pagePath . '; Data: ' . $dataJSON);

		// -- Anonymous functions --

		$result = function($reason) use ($response)
		{
			self::$logger->addInfo('Result of action: ' . $reason);

			return $response->withJSON([
				'result' => $reason
			]);
		};

		// -- Validate --

		if (!DataUtilities::isNonEmptyString($action) || !DataUtilities::isNonEmptyString($pagePath))
			return $result("'action' and 'page_name' must be non-empty strings");

		if (!is_array($data))
			return $result("'data' must be a JSON object/array");

		self::$logger->addInfo('Data array: ' . json_encode($data));

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
