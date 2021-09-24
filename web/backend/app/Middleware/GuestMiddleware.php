<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\ResponseUtilities;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class GuestMiddleware extends Middleware
{
	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$logger = $this->loggers['logger'];

		if ( $this->container->get( 'auth' )->isAuthenticated() )
		{
			$logger->info( 'Failed to proceed as not a guest' );

			$path = $this->container->router->pathFor( 'home' );

			return ResponseUtilities::respondWithRedirect( null, $path );
		}

		$logger->info( 'Is a guest so proceeding' );

		$response = $handler->handle( $request );
		return $response;
	}
}
