<?php

namespace App\Middleware;

use App\Globals\FrontEndParametersFacade;

class CsrfViewMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		FrontEndParametersFacade::setCsrfHtml(
			'<input type="hidden" name="' . $this->container->csrf->getTokenNameKey()
			. '"  value="' . $this->container->csrf->getTokenName() 
			. '"> <input type="hidden" name="' . $this->container->csrf->getTokenValueKey() 
			. '" value="' . $this->container->csrf->getTokenValue() . '">'
		);

		$response = $next($request, $response);
		return $response;
	}
}