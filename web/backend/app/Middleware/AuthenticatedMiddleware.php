<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\ResponseUtilities;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class AuthenticatedMiddleware extends Middleware
{
	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$logger = $this->loggers['logger'];

		if ( !$this->container->get( 'auth' )->isAuthenticated() )
		{
			$logger->info( 'Failed to proceed as not authenticated' );

			// TODO
			//$this->container->get('flash')->addMessage(
			//	'error',
			//	'Please sign in before doing that.'
			//);

			$route = $this->container->router->pathFor( 'auth.signin' );

			return ResponseUtilities::respondWithRedirect( null, $route );
		}

		$logger->info( 'Is authenticated so proceeding' );

		$response = $handler->handle( $request );
		return $response;
	}
}
