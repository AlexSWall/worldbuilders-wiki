<?php declare( strict_types = 1 );

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

abstract class Middleware implements MiddlewareInterface
{
	protected ContainerInterface $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/* e.g. give controllers access to view. */
	public function __get(string $property): mixed
	{
		if ( $this->container->get($property) )
		{
			return $this->container->get($property);
		}
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Assert ServerRequestInterface $request is a Slim 'Request'.
		return $this->route($request, $handler);
	}

	abstract public function route(Request $request, RequestHandlerInterface $handler): ResponseInterface;
}
