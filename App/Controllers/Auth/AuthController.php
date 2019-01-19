<?php

namespace App\Controllers\Auth;

use Respect\Validation\Validator as v;

use App\Models\User;
use App\Controllers\Controller;
use App\Models\UserPermission;

class AuthController extends Controller
{
	public function getSignup($request, $response)
	{
		return $this->view->render($response, 'auth/signup.twig');
	}

	public function postSignup($request, $response)
	{
		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
			'name' => v::noWhitespace()->notEmpty()->alpha(),
			'password' => v::noWhitespace()->notEmpty()
		]);

		if ( $validation->hasFailed() )
		{
			/* Redirect back */
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}

		$identifier = $this->container->randomlib->generateString(128);

		$user = User::create([
			'email' => $request->getParam('email'),
			'name' => $request->getParam('name'),
			'password' => $this->HashUtil->hashPassword($request->getParam('password')),
			'active' => false,
			'active_hash' => $this->HashUtil->hash($identifier)
		]);

		$user->permissions()->create(UserPermission::$defaults);

		/* $this->auth->attempt($user->email, $request->getParam('password')); */

		$this->mailer->send(
			'email/auth/registered.twig', 
			['user' => $user, 'identifier' => $identifier],
			function($message) use ($user)
			{
				$message->to($user->email, $user->name);
				$message->subject('Thanks for registering.');
			}
		);

		$this->flash->addMessage('info', 'You have signed up!  Please activate your account.');

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignIn($request, $response)
	{
		return $this->view->render($response, 'auth/signin.twig');
	}

	public function postSignIn($request, $response)
	{
		$auth = $this->auth->attempt(
			$request->getParam('email'),
			$request->getParam('password')
		);

		if (!$auth)
		{
			$this->flash->addMessage('error', 'Unable to sign in.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		if ($request->getParam('remember') === 'on')
			$response = $this->auth->setRememberCookie($response, $request->getParam('email'));

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignOut($request, $response)
	{
		$response = $this->auth->logout($request, $response);

		return $response->withRedirect($this->router->pathFor('home'));
	}
}