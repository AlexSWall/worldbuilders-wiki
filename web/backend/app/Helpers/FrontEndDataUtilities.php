<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Globals\GlobalsFacade;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * A collection of public static helpers for getting front-end data.
 */
class FrontEndDataUtilities
{
	/* == Getters for Structured Globally-Set Data == */

	public static function getBaseData(): array
	{
		$user = GlobalsFacade::getUserData();

		return [
			'preferredName' => ($user === null) ? null : $user->getUserDetails()->getPreferredName(),
			'isAuthenticated' => GlobalsFacade::getIsAuthenticated(),
			'csrfTokens' => GlobalsFacade::getCsrfTokens()
		];
	}

	/* == Non-Global Functions */
	/**
	 * These functions do not pull from global state.
	 */

	public static function getEntryPointResponse( \Slim\Views\Twig $view, Response $response, string $entryPointName, array $args = [] ): Response
	{
		return $view->render(
				$response,
				'indexes/' . $entryPointName . '.index.twig',
			array_merge( FrontEndDataUtilities::getBaseData(), $args )
		);
	}

	public static function constructEndpointDataArray( string $urlPath, string $title, string $html ): array
	{
		return [
			'urlPath' => $urlPath,
			'title' => $title,
			'html' => $html
		];
	}
}
