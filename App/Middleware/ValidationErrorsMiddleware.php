<?php

namespace App\Middleware;

use App\Globals\FrontEndParametersFacade;

class ValidationErrorsMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if ( isset($_SESSION['errors']) )
		{
			$errors = $_SESSION['errors'];
			unset($_SESSION['errors']);
		}
		else
			$errors = '';

		FrontEndParametersFacade::setErrors($errors);

		$response = $next($request, $response);
		return $response;
	}
}