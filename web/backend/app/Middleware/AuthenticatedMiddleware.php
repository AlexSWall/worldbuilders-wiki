<?php

namespace App\Middleware;

class AuthenticatedMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$logger = $this->loggers['logger'];

		if ( !$this->container->auth->isAuthenticated() )
		{
			$logger->addInfo('Failed to proceed as not authenticated');

			$this->container->flash->addMessage('error', 'Please sign in before doing that.');
			return $response->withRedirect($this->container->router->pathFor('auth.signin'));
		}

		$logger->addInfo('Is authenticated so proceeding');

		$response = $next($request, $response);
		return $response;
	}
}
