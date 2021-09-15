<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class WikiController extends Controller
{
	private static \App\Logging\Logger $logger;

	/* GET request with path of the form /#{pageName} */
	public function serveWikiApp(
		Request $request,
		Response $response
	): Response
	{
		self::$logger->info( 'Received GET request for Wiki app' );

		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'wiki' );
	}

	/* GET request with path of the form /w/{pageName} */
	public function serveWikiContentGetRequest(
		Request $request,
		Response $response,
		array $args
	): Response
	{
		$pagePath = $args['wikipage'];

		$character = $this->auth->getCharacter();
		$viewingPermissions = ( $character ? $character->getPermissions() : null );

		return WikiPageController::getWikiPageDataResponse(
			$response,
			$pagePath,
			$viewingPermissions
		);
	}

	/* GET request with path of the form /a/wiki */
	public function serveEditWikiPageGetRequest(
		Request $request,
		Response $response
	): Response
	{
		$pagePath = $request->getQueryParams()['wikipage'];

		return WikiPageController::getEditWikiPageResponse( $response, $pagePath );
	}

	/* POST request with path of the form /a/wiki */
	public function serveModifyWikiContentPostRequest(
		Request $request,
		Response $response
	): Response
	{
		$stringCheck = \App\Utilities\APIUtilities::$isStringValidator;
		$nonEmptyStringCheck = \App\Utilities\APIUtilities::$isNonEmptyStringValidator;

		$actionStructures = [
			'create' => [
				[ '\App\Controllers\WikiPageController', 'createWikiPage' ],
				[
					'page_path' => $nonEmptyStringCheck,
					'title' => $stringCheck
				]
			],
			'modify' => [
				[ '\App\Controllers\WikiPageController', 'modifyWikiPage' ],
				[
					'page_path' => $nonEmptyStringCheck,
					'title' => $stringCheck,
					'content' => $stringCheck
				]
			],
			'delete' => [
				[ '\App\Controllers\WikiPageController', 'deleteWikiPage' ],
				[
					'page_path' => $nonEmptyStringCheck,
				]
			]
		];

		$entryFunc = \App\Utilities\APIUtilities::createPostAPIEntryPoint(
			'Modify Wiki Pages',
			$actionStructures
		);

		return $entryFunc( $request, $response );
	}

	/* GET request with path of the form /a/wiki */
	public function serveGetInfoboxStructureNamesGetRequest(
		Request $request,
		Response $response
	): Response
	{
		return InfoboxController::getInfoboxStructureNamesResponse( $response );
	}

	public function serveGetInfoboxStructureGetRequest(
		Request $request,
		Response $response
	): Response
	{
		$infoboxName = $request->getQueryParam( 'infobox_name' );

		return InfoboxController::getInfoboxStructureResponse( $response, $infoboxName );
	}

	/* POST request with path of the form /a/infobox */
	public function serveModifyInfoboxStructurePostRequest(
		Request $request,
		Response $response
	): Response
	{
		$stringCheck = \App\Utilities\APIUtilities::$isStringValidator;
		$nonEmptyStringCheck = \App\Utilities\APIUtilities::$isNonEmptyStringValidator;

		$actionStructures = [
			'create' => [
				[ '\App\Controllers\InfoboxController', 'createInfobox' ],
				[
					'infobox_name' => $nonEmptyStringCheck
				]
			],
			'modify' => [
				[ '\App\Controllers\InfoboxController', 'modifyInfobox' ],
				[
					'infobox_name' => $nonEmptyStringCheck,
					'structure' => $stringCheck
				]
			],
			'delete' => [
				[ '\App\Controllers\InfoboxController', 'deleteInfobox' ],
				[
					'infobox_name' => $nonEmptyStringCheck,
				]
			]
		];

		$entryFunc = \App\Utilities\APIUtilities::createPostAPIEntryPoint(
			'Modify Infobox Structures',
			$actionStructures
		);

		return $entryFunc( $request, $response );
	}
}
