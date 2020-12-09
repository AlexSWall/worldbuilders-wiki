<?php

namespace App\Controllers;

use App\Models\WikiPage;
use App\Helpers\FrontEndDataUtilities;
use App\Helpers\DataUtilities;
use App\Helpers\ResponseUtilities;

class WikiController extends Controller
{
	static $logger;

	/* GET request with path of the form /#{pageName} */
	public function serveWikiApp($request, $response)
	{
		self::$logger->addInfo('Received GET request for Wiki app');

		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'wiki' );
	}

	/* GET request with path of the form /w/{pageName} */
	public function serveWikiContentGetRequest($request, $response, $args)
	{
		$pagePath = $args['wikipage'];
		self::$logger->addInfo('Received GET request for standard wikipage data of page ' . $pagePath);

		$character = $this->auth->getCharacter();
		$viewingPermissions = ( $character ? $character->getPermissions() : null );

		return WikiPageController::getWikiPageDataResponse($response, $pagePath, $viewingPermissions);
	}

	/* GET request with path of the form /a/wiki */
	public function serveWikitext($request, $response, $args)
	{
		$pagePath = $request->getQueryParam('wikipage');
		self::$logger->addInfo('Received GET request for wikitext of page ' . $pagePath);

		return WikiPageController::getWikiTextResponse($response, $pagePath);
	}

	/* POST request with path of the form /a/wiki */
	public function serveModifyWikiContentPostRequest($request, $response)
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$pagePath = trim($parsedBody['page_path']);
		$data = $parsedBody['data'];

		self::$logger->addInfo('Received POST request to modify wiki content for ' . $pagePath);
		self::$logger->addInfo('Action: ' . $action . '; Page Path: ' . $pagePath . '; Data: ' . $dataJSON);

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError(self::$logger, $response, $errorCode, $error);
		};

		// -- Validate --

		if (!DataUtilities::isNonEmptyString($action) || !DataUtilities::isNonEmptyString($pagePath))
			return $errorResponse(400, "'action' and 'page_name' must be non-empty strings");

		if (!is_array($data))
			return $errorResponse(400, "'data' must be a JSON object/array");

		self::$logger->addInfo('Data array: ' . json_encode($data));

		// -- Act --
		switch ($action)
		{
			case 'create':
				$title = $data['title'];
				if (!is_string($title))
					return $errorResponse(400, "'create' action needs data with 'title' key and string value");

				return WikiPageController::createWikiPage($response, $pagePath, $title);

			case 'modify':
				// Sets $title and $content
				foreach (['title', 'content'] as $key)
				{
					$$key = $data[$key];
					if (!is_string($$key))
						return $errorResponse(400, "'modify' action needs data with '" . $key . "' key and string value");
				}

				return WikiPageController::modifyWikiPage($response, $pagePath, $title, $content);

			case 'delete':
				return WikiPageController::deleteWikiPage($response, $pagePath);

			default:
				return $errorResponse(501, "action must be one of 'create', 'modify', or 'delete'");
		}

		// This shouldn't happen
		return $errorResponse(500, "server error");
	}
}
