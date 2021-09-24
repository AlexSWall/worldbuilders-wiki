<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Globals\FrontEndParametersFacade;
use App\Helpers\ResponseUtilities;
use App\Models\User;

use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class RememberMeMiddleware extends Middleware
{
	public static \App\Logging\Logger $logger;

	private function getRememberMeToken( Request $request ): ?Cookie
	{
		$cookies = Cookies::fromRequest( $request );
		self::$logger->dump( $cookies );

		$rememberMeCookieKey = $this->container->get( 'settings' )[ 'auth' ][ 'remember' ];
		self::$logger->dump( $rememberMeCookieKey );

		$rememberMeCookie = $cookies->get( $rememberMeCookieKey );
		self::$logger->dump( $rememberMeCookie );

		return $rememberMeCookie;
	}

	public function route( Request $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$rememberMeCookie = $this->getRememberMeToken( $request );

		FrontEndParametersFacade::setHasRememberMeCookie( $rememberMeCookie !== null );

		if ( ! $this->auth->isAuthenticated() && $this->attemptLogin( $rememberMeCookie ) )
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
	public function attemptLogin( ?Cookie $rememberMeCookie ): bool
	{
		$logger = $this->loggers['logger'];

		if ( $rememberMeCookie === null )
		{
			$logger->info( 'Not authenticated but no \'remember me\' cookie.' );

			return false;
		}

		$data = $rememberMeCookie->getValue();

		$credentials = explode( '___', $data );

		if ( count( $credentials ) !== 2 )
		{
			$logger->info( 'Not authenticated but \'remember me\' cookie contains wrong number of sections.' );

			return false;
		}

		$identifier = $credentials[0];
		$token = $this->HashingUtilities->hash( $credentials[1] );

		$user = User::retrieveUserByRememberMeIdentifier( $identifier );

		if ( !$user )
		{
			$logger->info( 'Not authenticated but failed to retrieve user by \'remember me\' identifier.' );

			return false;
		}

		if ( !$this->HashingUtilities->checkHash( $token, $user->getRememberMeToken() ) )
		{
			$logger->info( 'Not authenticated but hash of section 1 does not equal user\'s \'remember me\' token.' );

			$user->removeRememberMeCredentials();
			return false;
		}

		$logger->info( 'Not authenticated but successfully authentication from the user\'s \'remember me\' token.' );

		$_SESSION[$this->container->get( 'settings' )['auth']['session']] = $user->getUserId();
		return true;
	}
}
