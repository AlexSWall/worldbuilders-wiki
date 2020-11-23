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
			$this->loggers['logger']->addInfo('Failed to authenticate as an admin');

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

		$this->loggers['logger']->addInfo('Authenticated as an admin');

		$response = $next($request, $response);
		return $response;
	}
}
