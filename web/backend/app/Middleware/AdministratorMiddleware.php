<?php

namespace App\Middleware;

class AdministratorMiddleware extends Middleware
{
	private $isApiRoute;

	public function __construct($container, $isApiRoute)
	{
		parent::__construct($container);

		$this->isApiRoute = $isApiRoute;
	}

	public function __invoke($request, $response, $next)
	{
		if ( !$this->container->auth->isAuthenticated() || !$this->container->auth->getUser()->isAdmin() )
		{
			if ($isApiRoute)
			{
				return $response->withJSON([], 401, JSON_UNESCAPED_UNICODE);
			}
			else
			{
				$this->container->flash->addMessage('error', 'You must be an administrator to access that page!');
				return $response->withRedirect($this->container->router->pathFor('home'));
			}
		}

		$response = $next($request, $response);
		return $response;
	}
}
