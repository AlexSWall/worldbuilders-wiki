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
		if ($this->auth->check())
			return false;

		$rememberMeCookie = FigRequestCookies::get($request, $this->container->get('settings')['auth']['remember']);
		$data = $rememberMeCookie->getValue();

		if (is_null($data))
			return false;

		$credentials = explode('___', $data);

		if (empty(trim($data)) || count($credentials) !== 2)
			return false;

		$identifier = $credentials[0];
		$token = $this->HashUtil->hash($credentials[1]);

		$user = User::where('remember_identifier', $identifier)
			->first();

		if (!$user)
			return false;

		if (!$this->HashUtil->checkHash($token, $user->remember_token))
		{
			$user->removeRememberCredentials();
			return false;
		}

		$_SESSION[$this->container->get('settings')['auth']['session']] = $user->id;
		return true;
	}
}