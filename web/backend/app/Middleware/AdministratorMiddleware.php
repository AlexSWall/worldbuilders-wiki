<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\ResponseUtilities;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class AdministratorMiddleware extends Middleware
{
	static $logger;

	private bool $isApiRoute;

	public function __construct( ContainerInterface $container, bool $isApiRoute )
	{
		parent::__construct( $container );

		$this->isApiRoute = $isApiRoute;
	}

	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$auth = $this->container->get( 'auth' );

		$user = $auth->getUser();

		if ( ! $user || ! $user->isAdmin() )
		{
			self::$logger->info( 'Failed to authenticate as an admin' );

			if ( $this->isApiRoute )
			{
				return ResponseUtilities::getApiErrorResponse( null, 401, 'Not authenticated as an admin' );
			}
			else
			{
				$path = $this->container->router->pathFor( 'home' );

				return ResponseUtilities::respondWithRedirect( null, $path );
			}
		}

		self::$logger->info( 'Authenticated as an admin' );

		$response = $handler->handle( $request );
		return $response;
	}
}
