<?php

declare(strict_types=1);

namespace App\Middleware;

use Slim\Psr7\Response;

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

			return (new Response())->withHeader( 'Location', $this->container->router->pathFor( 'home' ) )->withStatus( 302 );
		}

		$logger->info( 'Is a guest so proceeding' );

		$response = $handler->handle( $request );
		return $response;
	}
}
