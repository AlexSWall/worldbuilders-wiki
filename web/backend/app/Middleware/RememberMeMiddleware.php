<?php

namespace App\Middleware;

use Dflydev\FigCookies\FigRequestCookies;

use App\Models\User;

class RememberMeMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$beingLoggedIn = $this->attemptLogin($request);

		if ( $beingLoggedIn )
			/* The request should be resent to include the new session cookie in rendering the page. */
			return $response->withRedirect($request->getUri()->getPath());

		$response = $next($request, $response);
		return $response;
	}

/**
 * Attempts to log the client into their account if they have a 'Remember Me' cookie in the request
 * but include no session id cookie by adding the account to the newly created session.
 * 
 * @return boolean 
 		Returns 'True' if the account corresponding to the cookie is successfully logged into.
 		Returns 'False' otherwise.
 */
	public function attemptLogin($request)
	{
		if ($this->auth->isAuthenticated())
			return false;

		$logger = $this->loggers['logger'];

		$rememberMeCookie = FigRequestCookies::get($request, $this->container->get('settings')['auth']['remember']);
		$data = $rememberMeCookie->getValue();

		if ( is_null($data) || empty(trim($data)) )
		{
			$logger->addInfo('Not authenticated but no \'remember me\' cookie.');

			return false;
		}

		$credentials = explode('___', $data);

		if ( count($credentials) !== 2 )
		{
			$logger->addInfo('Not authenticated but \'remember me\' cookie contains wrong number of sections.');

			return false;
		}

		$identifier = $credentials[0];
		$token = $this->HashingUtilities->hash($credentials[1]);

		$user = User::retrieveUserByRememberMeIdentifier($identifier);

		if ( !$user )
		{
			$logger->addInfo('Not authenticated but failed to retrieve user by \'remember me\' identifier.');

			return false;
		}

		if ( !$this->HashingUtilities->checkHash($token, $user->getRememberMeToken()) )
		{
			$logger->addInfo('Not authenticated but hash of section 1 does not equal user\'s \'remember me\' token.');

			$user->removeRememberMeCredentials();
			return false;
		}

		$logger->addInfo('Not authenticated but successfully authentication from the user\'s \'remember me\' token.');

		$_SESSION[$this->container->get('settings')['auth']['session']] = $user->getUserId();
		return true;
	}
}
