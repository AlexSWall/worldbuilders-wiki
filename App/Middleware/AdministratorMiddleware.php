<?php

namespace App\Middleware;

class AdministratorMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if ( !$this->container->auth->check() || !$this->container->auth->user()->isAdmin() )
		{
			$this->container->flash->addMessage('error', 'You must be an administrator to access that page!');
			return $response->withRedirect($this->container->router->pathFor('home'));
		}

		$response = $next($request, $response);
		return $response;
	}
}