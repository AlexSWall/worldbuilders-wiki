<?php

namespace App\Helpers;

use App\Globals\FrontEndParametersFacade;

/**
 * A collection of public static helpers for getting front-end data.
 */ 
class FrontEndDataUtils
{
	/* == Getters for Structured Globally-Set Data == */
	/**
	 * These functions all pull from global state.
	 */

	public static function getBaseData()
	{
		return [
			'baseUrl' => FrontEndParametersFacade::getBaseUrl(),
			'auth' => [ 
				'check' => FrontEndParametersFacade::getIsAuthenticated(),
				'user' => FrontEndParametersFacade::getUserData()
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

	public static function getWebpageDataFor($webpageName, $webpageTitle, $view = null, $filePath = null)
	{
		if ( !is_null($view) && !is_null($filePath) )
			$html = $view->fetch("SpecialWebpages/{$filePath}.html");
		else
			$html = '';

		return [ 
			'name' => $webpageName,
			'title' => $webpageTitle,
			'HTML' => $html
		];
	}
}