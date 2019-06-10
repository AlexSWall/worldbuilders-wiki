<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use App\Models\User;
use App\Helpers\FormUtilities;

use App\Validation\Rules as Rules;

class AuthenticationController extends Controller
{
	static $logger;

	public function getSignup($request, $response)
	{
		return FormUtilities::getForm($this->view, $response, [
			'title' => 'Sign Up',
			'formType' => 'Sign Up'
		]);
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
		return FormUtilities::getForm($this->view, $response, [
			'title' => 'Sign In',
			'formType' => 'Sign In'
		]);
	}

	public function postSignIn($request, $response)
	{
		$userIdentity = $request->getParam('identity');

		$userExists = $this->auth->checkUserExists($userIdentity);

		if (!$userExists)
		{
			$this->flash->addMessage('error', 'Unabled to sign in.');
			return $response->withRedirect('/Sign_In');
		}

		$activated = $this->auth->checkActivated($userIdentity);

		if (!$activated)
		{
			$this->flash->addMessage('error', 'Account not yet activated. Check your emails for the account activation link.');
			return $response->withRedirect('/Sign_In');
		}

		$auth = $this->auth->attempt($userIdentity, $request->getParam('password'));

		if (!$auth)
		{
			$this->flash->addMessage('error', 'Unable to sign in.');
			return $response->withRedirect('/Sign_In');
		}

		if ($request->getParam('remember') === 'on')
			$response = $this->auth->setRememberMeCookie($response, $request->getParam('identity'));

		return $response->withRedirect('/Home');
	}

	public function getSignOut($request, $response)
	{
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