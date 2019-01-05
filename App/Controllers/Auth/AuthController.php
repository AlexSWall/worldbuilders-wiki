<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

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

		$user = User::create([
			'email' => $request->getParam('email'),
			'name' => $request->getParam('name'),
			'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT, ['cost' => 10])
		]);

		$this->auth->attempt($user->email, $request->getParam('password'));

		$this->mailer->send('email/auth/registered.php', ['user' => $user], function($message) use ($user)
		{
			$message->to($user->email, $user->name);
			$message->subject('Thanks for registering.');
		});

		$this->flash->addMessage('info', 'You have signed up!');

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

		if ($auth)
			return $response->withRedirect($this->router->pathFor('home'));
		else
		{
			$this->flash->addMessage('error', 'Unable to sign in.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}
	}

	public function getSignOut($request, $response)
	{
		$this->auth->logout();

		return $response->withRedirect($this->router->pathFor('home'));
	}
}