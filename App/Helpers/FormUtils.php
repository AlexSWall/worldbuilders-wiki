<?php

namespace App\Helpers;

class FormUtils
{
	public static function getForm($view, $response, $args)
	{
		//var_dump($args);
		return $view->render($response, 'Indexes/authentication.index.twig', 
			array_merge( FrontEndDataUtils::getBaseData(),
				[ 
					'formProperties' => array_merge(
						FrontEndDataUtils::getFormData(),
						$args
					)
				]
			)
		);
	}
}