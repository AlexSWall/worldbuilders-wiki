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
}
