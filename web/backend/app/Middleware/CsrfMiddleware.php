<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Globals\GlobalsFacade;

use Slim\Csrf\Guard;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class CsrfMiddleware extends Middleware
{
	public static \App\Logging\Logger $logger;

	private Guard $csrf;

	public function __construct( ContainerInterface $container, Guard $csrf )
	{
		parent::__construct( $container );
		$this->csrf = $csrf;
	}

	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$csrf = $this->csrf;

		$tokens = [
			$csrf->getTokenNameKey()  => $csrf->getTokenName(),
			$csrf->getTokenValueKey() => $csrf->getTokenValue()
		];

		GlobalsFacade::setCsrfTokens( $tokens );

		self::$logger->info( 'CSRF Tokens:' );
		self::$logger->dump( $tokens );

		$response = $handler->handle( $request );
		return $response;
	}
}
