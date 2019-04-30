<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\UserPermissions;
use App\Models\UserDetails;

use App\Controllers\Controller;

use App\Validation\Rules as Rules;

class AuthController extends Controller
{
	static $logger;

	public function getSignup($request, $response)
	{
		return $this->view->render($response, 'auth/signup.twig', ['title' => "Sign Up"]);
	}

	/* Receives by reference */
	public static function cleanSignupCredentials(& $params)
	{
		$params['preferred_name'] = trim(preg_replace('/\s+/', ' ', $params['preferred_name']));
	}

	public function postSignup($request, $response)
	{
		$signupParams = $request->getParams();

		self::cleanSignupCredentials($signupParams); /* Passes by reference */

		$validation = $this->validator->validate($signupParams, [
			'preferred_name' => Rules::preferredNameRules(),
			'username' => Rules::usernameAvailableRules(),
			'email' => Rules::emailAvailableRules(),
			'password' => Rules::passwordRules(),
			'password_confirm' => Rules::passwordConfirmationRules($signupParams['password']),
		]);

		if ( $validation->hasFailed() )
			return $response->withRedirect($this->router->pathFor('auth.signup'));

		$identifier = $this->container->randomlib->generateString(128);

		$user = User::createInactiveUser(
			$signupParams['username'],
			$signupParams['email'],
			$this->HashUtil->hashPassword($signupParams['password']),
			$this->HashUtil->hash($identifier)
		);

		$user->permissions()->create(UserPermissions::$defaults);

		$user->details()->create(UserDetails::createUserDetailsArray([
			'preferred_name' => $signupParams['preferred_name']
		]));

		/* $this->auth->attempt($user->email, $request->getParam('password')); */

		$this->mailer->send(
			'email/auth/registered.twig', 
			['user' => $user, 'identifier' => $identifier],
			function($message) use ($user)
			{
				$message->to($user->getEmail(), $user->details()->getPreferredName());
				$message->subject('Thanks for registering.');
			}
		);

		$this->flash->addMessage('info', 'You have signed up!  Please activate your account.');

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignIn($request, $response)
	{
		return $this->view->render($response, 'auth/signin.twig', ['title' => "Sign In"]);
	}

	public function postSignIn($request, $response)
	{
		$activated = $this->auth->checkActivated($request->getParam('identity'));

		if (!$activated)
		{
			$this->flash->addMessage('error', 'Account not yet activated. Check your emails for the account activation link.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		$auth = $this->auth->attempt(
			$request->getParam('identity'),
			$request->getParam('password')
		);

		if (!$auth)
		{
			$this->flash->addMessage('error', 'Unable to sign in.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		if ($request->getParam('remember') === 'on')
			$response = $this->auth->setRememberCookie($response, $request->getParam('identity'));

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignOut($request, $response)
	{
		$response = $this->auth->logout($request, $response);

		return $response->withRedirect($this->router->pathFor('home'));
	}
}