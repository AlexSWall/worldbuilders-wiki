<?php

namespace App\Helpers;

use App\Globals\FrontEndParametersFacade;

/**
 * A collection of public static helpers for getting front-end data.
 */ 
class FrontEndDataUtilities
{
	/* == Getters for Structured Globally-Set Data == */
	/**
	 * These functions all pull from global state.
	 */

	public static function getBaseData()
	{
		$user = FrontEndParametersFacade::getUserData();
		if ( is_null($user) )
			$userDetailsData = [];
		else
		{
			$userDetails = $user->getUserDetails();
			$userDetailsData = [
				'preferredName' => $userDetails->getPreferredName()
			];
		}

		return [
			'baseUrl' => FrontEndParametersFacade::getBaseUrl(),
			'authenticationData' => [ 
				'isAuthenticated' => FrontEndParametersFacade::getIsAuthenticated(),
				'userData' => $userDetailsData
			],
			'flash' => FrontEndParametersFacade::getFlash()
		];
	}

	public static function getFormData()
	{
		return [ 
			'csrfHTML'  => FrontEndParametersFacade::getCsrfHtml(),
			'oldValues' => FrontEndParametersFacade::getPreviousParameters(),
			'errors'    => FrontEndParametersFacade::getErrors()
		];
	}

	public static function getCSRFData()
	{
		return [ 'csrfHTML' => FrontEndParametersFacade::getCsrfHtml() ];
	}

	/* == Non-Global Functions */

	public static function getEntryPointResponse($view, $response, $entryPointName, $args = [])
	{
		return $view->render($response, 'Indexes/' . $entryPointName . '.index.twig', 
			array_merge( FrontEndDataUtilities::getBaseData(), $args )
		);
	}

	public static function getWikiPageDataFor($urlPath, $title, $view = null, $filePath = null)
	{
		if ( !is_null($view) && !is_null($filePath) )
			$html = $view->fetch("SpecialWikiPages/{$filePath}.html");
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