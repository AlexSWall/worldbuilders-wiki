<?php

namespace App\Helpers;

class FormUtils
{

	public static function getForm($view, $response, $args = [])
	{
		return $view->render($response, 'authentication/authentication.twig', 
			self::constructFrontendParametersArray($args));
	}

	private static function constructFrontendParametersArray($args)
	{
		return array_merge([
			'auth' => $GLOBALS['auth'],
			'flash' => $GLOBALS['flash'],
			'baseUrl' => $GLOBALS['baseUrl']
		],
		self::getFormProperties($args));
	}

	private static function getFormProperties($args)
	{
		return ['formProperties' => array_merge(
			[
				'oldValues' => $GLOBALS['previous_params'],
				'errors' => $GLOBALS['errors']
			], $args)
		];
	}
}