<?php

namespace App\Helpers;

use App\Helpers\FrontendUtils;

class FormUtils
{
	public static function getForm($view, $response, $args = [])
	{
		return $view->render($response, 'Indexes/authentication.index.twig', 
			FrontendUtils::constructFrontendParametersArray(self::addFormProperties($args)));
	}

	private static function addFormProperties($args)
	{
		return ['formProperties' => array_merge(
			[
				'oldValues' => $GLOBALS['previous_params'],
				'errors' => $GLOBALS['errors']
			], $args)
		];
	}
}