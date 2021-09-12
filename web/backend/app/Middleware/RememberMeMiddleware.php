<?php declare( strict_types = 1 );

namespace App\Middleware;

use App\Models\User;

use Dflydev\FigCookies\FigRequestCookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Psr7\Response as PsrResponse;
use Slim\Http\ServerRequest as Request;

class RememberMeMiddleware extends Middleware
{
	public function route(Request $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$beingLoggedIn = $this->attemptLogin($request);

		if ( $beingLoggedIn )
			/* The request should be resent to include the new session cookie in rendering the page. */
			return (new PsrResponse())->withHeader('Location', $request->getUri()->getPath())->withStatus(302);

		$response = $handler->handle($request);
		return $response;
	}

	/**
	 * Attempts to log the client into their account if they have a 'Remember Me' cookie in the request
	 * but include no session id cookie by adding the account to the newly created session.
	 * 
	 * @return Returns true if the account corresponding to the cookie is
	 * successfully logged into, otherwise it returns false.
	 */
	public function attemptLogin(Request $request): bool
	{
		if ($this->auth->isAuthenticated())
			return false;

		$logger = $this->loggers['logger'];

		$rememberMeCookie = FigRequestCookies::get($request, $this->container->get('settings')['auth']['remember']);
		$data = $rememberMeCookie->getValue();

		if ( is_null($data) || empty(trim($data)) )
		{
			$logger->info('Not authenticated but no \'remember me\' cookie.');

			return false;
		}

		$credentials = explode('___', $data);

		if ( count($credentials) !== 2 )
		{
			$logger->info('Not authenticated but \'remember me\' cookie contains wrong number of sections.');

			return false;
		}

		$identifier = $credentials[0];
		$token = $this->HashingUtilities->hash($credentials[1]);

		$user = User::retrieveUserByRememberMeIdentifier($identifier);

		if ( !$user )
		{
			$logger->info('Not authenticated but failed to retrieve user by \'remember me\' identifier.');

			return false;
		}

		if ( !$this->HashingUtilities->checkHash($token, $user->getRememberMeToken()) )
		{
			$logger->info('Not authenticated but hash of section 1 does not equal user\'s \'remember me\' token.');

			$user->removeRememberMeCredentials();
			return false;
		}

		$logger->info('Not authenticated but successfully authentication from the user\'s \'remember me\' token.');

		$_SESSION[$this->container->get('settings')['auth']['session']] = $user->getUserId();
		return true;
	}
}
