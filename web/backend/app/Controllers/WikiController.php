<?php declare( strict_types = 1 );

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;
use App\Helpers\DataUtilities;
use App\Helpers\ResponseUtilities;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class WikiController extends Controller
{
	static \App\Logging\Logger $logger;

	/* GET request with path of the form /#{pageName} */
	public function serveWikiApp(Request $request, Response $response): Response
	{
		self::$logger->info('Received GET request for Wiki app');

		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'wiki' );
	}

	/* GET request with path of the form /w/{pageName} */
	public function serveWikiContentGetRequest(Request $request, Response $response, array $args): Response
	{
		$pagePath = $args['wikipage'];
		self::$logger->info('Received GET request for standard wikipage data of page ' . $pagePath);

		$character = $this->auth->getCharacter();
		$viewingPermissions = ( $character ? $character->getPermissions() : null );

		return WikiPageController::getWikiPageDataResponse($response, $pagePath, $viewingPermissions);
	}

	/* GET request with path of the form /a/wiki */
	public function serveWikitext(Request $request, Response $response): Response
	{
		$pagePath = $request->getQueryParams()['wikipage'];
		self::$logger->info('Received GET request for wikitext of page ' . $pagePath);

		return WikiPageController::getWikiTextResponse($response, $pagePath);
	}

	/* POST request with path of the form /a/wiki */
	public function serveModifyWikiContentPostRequest(Request $request, Response $response): Response
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$pagePath = trim($parsedBody['page_path']);
		$data = $parsedBody['data'];

		self::$logger->info('Received POST request to modify wiki content for ' . $pagePath);
		self::$logger->info('Action: ' . $action . '; Page Path: ' . $pagePath . '; Data: ' . $data);

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		// -- Validate --

		if (!DataUtilities::isNonEmptyString($action) || !DataUtilities::isNonEmptyString($pagePath))
			return $errorResponse(400, "'action' and 'page_name' must be non-empty strings");

		if (!is_array($data))
			return $errorResponse(400, "'data' must be a JSON object/array");

		self::$logger->info('Data array: ' . json_encode($data));

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

	/* GET request with path of the form /a/wiki */
	public function serveInfoboxStructure(RequestInterface $request, ResponseInterface $response)
	{
		$infoboxName = $request->getQueryParam('infobox_name');
		self::$logger->addInfo('Received GET request for infobox structure for the infobox named ' . $infoboxName);

		return InfoboxStructureController::getInfoboxStructureResponse($response, $infoboxName);
	}

	/* POST request with path of the form /a/wiki */
	public function serveModifyInfoboxStructurePostRequest($request, $response)
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$infoboxName = trim($parsedBody['infobox_name']);
		$data = $parsedBody['data'];

		self::$logger->addInfo('Received GET request to modify the infobox structure for the infobox named ' . $infoboxName);
		self::$logger->addInfo('Action: ' . $action . '; Page Path: ' . $infoboxName . '; Data: ' . $data);

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		// -- Validate --

		if (!DataUtilities::isNonEmptyString($action) || !DataUtilities::isNonEmptyString($infoboxName))
			return $errorResponse(400, "'action' and 'infobox_name' must be non-empty strings");

		if (!is_array($data))
			return $errorResponse(400, "'data' must be a JSON object/array");

		self::$logger->addInfo('Data array: ' . json_encode($data));

		// -- Act --
		switch ($action)
		{
			case 'create':
				return InfoboxStructureController::createInfoboxStructure($response, $infoboxName);

			case 'modify':
				$infoboxStructure = $data['infobox_structure'];
				if (!is_string($infoboxStructure))
					return $errorResponse(400, $action + "action needs data with 'infobox_structure' key and string value");

				return InfoboxStructureController::modifyInfoboxStructure($response, $infoboxName, $infoboxStructure);

			case 'delete':
				return InfoboxStructureController::deleteInfoboxStructure($response, $infoboxName);

			default:
				return $errorResponse(501, "action must be one of 'create', 'modify', or 'delete'");
		}

		// This shouldn't happen
		return $errorResponse(500, "server error");
	}
}
