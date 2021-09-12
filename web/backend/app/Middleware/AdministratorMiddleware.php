<?php declare( strict_types = 1 );

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;
use Slim\Psr7\Response as PsrResponse;

class AdministratorMiddleware extends Middleware
{
	private bool $isApiRoute;

	public function __construct(ContainerInterface $container, bool $isApiRoute)
	{
		parent::__construct($container);

		$this->isApiRoute = $isApiRoute;
	}

	public function route(Request $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$logger = $this->loggers['logger'];

		if ( !$this->container->get('auth')->isAuthenticated() || !$this->container->get('auth')->getUser()->isAdmin() )
		{
			$logger->info('Failed to authenticate as an admin');

			if ($this->isApiRoute)
			{
				return (new PsrResponse())->withHeader('Location', [], 401, JSON_UNESCAPED_UNICODE)->withStatus(302);
			}
			else
			{
				return (new PsrResponse())->withHeader('Location', $this->container->get('router')->pathFor('home'))->withStatus(302);
			}
		}

		$logger->info('Authenticated as an admin');

		$response = $handler->handle($request);
		return $response;
	}
}
