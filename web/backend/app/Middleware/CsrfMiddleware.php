<?php

namespace App\Middleware;

use App\Globals\FrontEndParametersFacade;

class CsrfMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		FrontEndParametersFacade::setCsrfTokens([
			$this->container->csrf->getTokenNameKey() => $this->container->csrf->getTokenName(),
			$this->container->csrf->getTokenValueKey() => $this->container->csrf->getTokenValue()
		]);

		$response = $next($request, $response);
		return $response;
	}
}
