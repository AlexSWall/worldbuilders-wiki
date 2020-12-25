<?php

namespace App\Helpers;

use App\Globals\FrontEndParametersFacade;

/**
 * A collection of public static helpers for getting front-end data.
 */ 
class FrontEndDataUtilities
{
	/* == Getters for Structured Globally-Set Data == */

	public static function getBaseData()
	{
		$user = FrontEndParametersFacade::getUserData();

		return [
			'preferredName' => ($user === null) ? null : $user->getUserDetails()->getPreferredName(),
			'isAuthenticated' => FrontEndParametersFacade::getIsAuthenticated(),
			'csrfTokens' => FrontEndParametersFacade::getCsrfTokens(),
			'flash' => FrontEndParametersFacade::getFlash()
		];
	}

	/* == Non-Global Functions */
	/**
	 * These functions do not pull from global state.
	 */

	public static function getEntryPointResponse($view, $response, $entryPointName, $args = [])
	{
		return $view->render($response, 'indexes/' . $entryPointName . '.index.twig', 
			array_merge( FrontEndDataUtilities::getBaseData(), $args )
		);
	}

	public static function getWikiPageDataFor($urlPath, $title, $view = null, $filePath = null)
	{
		if ( !is_null($view) && !is_null($filePath) )
			$html = $view->fetch("special/{$filePath}.html");
		else
			$html = '';
		return self::constructEndpointDataArray($urlPath, $title, $html);
	}

	public static function constructEndpointDataArray($urlPath, $title, $html)
	{
		return [
			'urlPath' => $urlPath,
			'title' => $title,
			'html' => $html
		];
	}
}
