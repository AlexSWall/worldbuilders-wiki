<?php

namespace App\Helpers;

class FormUtils
{
	public static function getForm($view, $response, $args)
	{
		return FrontEndDataUtils::getEntryPointResponse( $view, $response, 'authentication', [ 
			'formProperties' => array_merge(
				FrontEndDataUtils::getFormData(),
				$args
			)
		]);
	}
}