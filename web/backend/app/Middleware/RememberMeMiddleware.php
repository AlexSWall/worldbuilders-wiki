<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Globals\GlobalsFacade;
use App\Helpers\ResponseUtilities;

use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class RememberMeMiddleware extends Middleware
{
	public static \App\Logging\Logger $logger;

	private function extractRememberMeToken( Request $request ): ?Cookie
	{
		$cookies = Cookies::fromRequest( $request );

		$rememberMeCookieKey = $this->container->get( 'settings' )[ 'auth' ][ 'remember' ];

		$rememberMeCookie = $cookies->get( $rememberMeCookieKey );

		return $rememberMeCookie;
	}

	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$rememberMeCookie = $this->extractRememberMeToken( $request );

		GlobalsFacade::setHasRememberMeCookie( $rememberMeCookie !== null );

		if ( ! $this->auth->isAuthenticated() && $this->attemptLoginUsingCookie( $rememberMeCookie ) )
		{
			// The request should be resent to include the new session cookie in
			// rendering the page.
			return ResponseUtilities::respondWithRedirect( null, $request->getUri()->getPath() );
		}

		$response = $handler->handle( $request );

		return $response;
	}

	/**
	 * Attempts to log the client into their account if their request includes a
	 * 'Remember Me' cookie but no authenticated session ID cookie, by adding the
	 * 'Remember Me' cookie's account to the newly created session.
	 *
	 * To make use of this, the client will need to reload their page via a
	 * redirect Response/etc.
	 *
	 * @return Returns true if the account corresponding to the cookie is
	 * successfully logged into, otherwise it returns false.
	 */
	public function attemptLoginUsingCookie( ?Cookie $rememberMeCookie ): bool
	{
		$logger = $this->loggers['logger'];

		if ( $rememberMeCookie === null )
		{
			$logger->info( 'No \'remember me\' cookie so cannot authenticate using one.' );

			return false;
		}

		$cookieData = $rememberMeCookie->getValue();

		$logger->info( '\'Remember me\' cookie present, attempting authentication' );

		return $this->auth->attemptLoginFromCookie( $cookieData );
	}
}
