<?php

namespace App\Middleware;

class GuestMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$logger = $this->loggers['logger'];

		if ($this->container->auth->isAuthenticated())
		{
			$logger->addInfo('Failed to proceed as not a guest');

			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		$logger->addInfo('Is a guest so proceeding');

		$response = $next($request, $response);
		return $response;
	}
}
