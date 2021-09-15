<?php

declare(strict_types=1);

namespace App\Middleware;

use Slim\Psr7\Response;

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
			//$this->container->get('flash')->addMessage('error', 'Please sign in before doing that.');
			return (new Response())->withHeader( 'Location', $this->container->router->pathFor( 'auth.signin' ) )->withStatus( 302 );
		}

		$logger->info( 'Is authenticated so proceeding' );

		$response = $handler->handle( $request );
		return $response;
	}
}
