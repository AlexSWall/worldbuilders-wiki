<?php

namespace App\Helpers;

class FormUtilities
{
	public static function getForm($view, $response, $args)
	{
		return FrontEndDataUtilities::getEntryPointResponse( $view, $response, 'authentication', [ 
			'formProperties' => array_merge(
				FrontEndDataUtilities::getFormData(),
				$args
			)
		]);
	}
}