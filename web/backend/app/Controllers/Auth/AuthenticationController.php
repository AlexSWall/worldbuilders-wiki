<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use App\Models\User;
use App\Helpers\FormUtilities;

use App\Validation\Rules as Rules;

class AuthenticationController extends Controller
{
	static $logger;
	static $debug_logger;

	public function getSignup($request, $response)
	{
		self::$debug_logger->addInfo('getSignup route');
		return FormUtilities::getForm($this->view, $response, [
			'title' => 'Sign Up',
			'formType' => 'Sign Up'
		]);
	}

	public function postSignup($request, $response)
	{
		self::$debug_logger->addInfo('postSignup route');
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
			return $response->withRedirect('/Sign_Up');

		$identifier = $this->container->randomlib->generateString(128);

		$user = User::createInactiveUser(
			$signupParams['username'],
			$signupParams['email'],
			$this->HashingUtilities->hashPassword($signupParams['password']),
			$this->HashingUtilities->hash($identifier)
		);

		$user->createUserPermissions();
		$user->createUserDetails($signupParams['preferred_name']);

		/* $this->auth->attempt($user->getEmail(), $request->getParam('password')); */

		$this->mailer->send(
			$user,
			'Thanks for registering!',
			'registered.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		$this->flash->addMessage('info', 'You have signed up!  Please activate your account.');

		return $response->withRedirect('/Home');
	}

	public function getSignIn($request, $response)
	{
		self::$debug_logger->addInfo('getSignIn route');
		return FormUtilities::getForm($this->view, $response, [
			'title' => 'Sign In',
			'formType' => 'Sign In'
		]);
	}

	public function postSignIn($request, $response)
	{
		self::$debug_logger->addInfo('postSignIn route');
		$userIdentity = $request->getParam('identity');

		$userExists = $this->auth->checkUserExists($userIdentity);

		if (!$userExists)
		{
			self::$debug_logger->addInfo('User does not exist, returning...');
			$this->flash->addMessage('error', 'Unabled to sign in.');
			return $response->withRedirect('/Sign_In');
		}

		self::$debug_logger->addInfo('User exists, checking activated...');
		$activated = $this->auth->checkActivated($userIdentity);

		if (!$activated)
		{
			self::$debug_logger->addInfo('User is not activated, returning...');
			$this->flash->addMessage('error', 'Account not yet activated. Check your emails for the account activation link.');
			return $response->withRedirect('/Sign_In');
		}

		self::$debug_logger->addInfo('User activated, checking authentication succeeds...');
		$auth = $this->auth->attempt($userIdentity, $request->getParam('password'));

		if (!$auth)
		{
			self::$debug_logger->addInfo('User did not authenticate, returning...');
			$this->flash->addMessage('error', 'Unable to sign in.');
			return $response->withRedirect('/Sign_In');
		}

		self::$debug_logger->addInfo('User authenticated...');
		if ($request->getParam('remember') === 'on')
			$response = $this->auth->setRememberMeCookie($response, $request->getParam('identity'));

		self::$debug_logger->addInfo('Returning a redirect to /Home');
		return $response->withRedirect('/Home');
	}

	public function getSignOut($request, $response)
	{
		self::$debug_logger->addInfo('getSignOut route');
		$response = $this->auth->logout($request, $response);
		return $response->withRedirect('/Home');
	}

	/* == Helpers == */

	/* Receives by reference */
	public static function cleanSignupCredentials(& $params)
	{
		$params['preferred_name'] = trim(preg_replace('/\s+/', ' ', $params['preferred_name']));
	}
}
