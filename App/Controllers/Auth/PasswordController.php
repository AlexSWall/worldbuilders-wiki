<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
	public function getChangePassword($request, $response)
	{
		return $this->view->render($response, 'auth/password/changepassword.twig');
	}

	public function postChangePassword($request, $response)
	{
		if ( !$this->auth->check() )
			return $response->withRedirect($this->router->pathFor('home'));

		$validation = $this->validator->validate($request, [
			'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->user()->password),
			'password' => v::noWhitespace()->notEmpty()
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.change'));

		$this->auth->user()->setPassword($request->getParam('password'));

		$this->flash->addMessage('info', 'Your password has been changed.');
		
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getPasswordRecovery($request, $response)
	{
		return $this->view->render($response, 'auth/password/passwordrecovery.twig');
	}

	public function postPasswordRecovery($request, $response)
	{
		$email = $request->getParam('email');

		$validation = $this->validator->validate($request, [
			'email' => v::noWhitespace()->notEmpty()->email()->emailInUse()
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.recovery'));

		$user = User::getUser($email);

		$identifier = $this->container->randomlib->generateString(128);
		$hashedIdentifier = $this->HashUtil->hash($identifier);
		
		$user->setPasswordRecoveryHash($hashedIdentifier);

		$this->mailer->send(
			'email/auth/recoverpassword.twig', 
			['user' => $user, 'identifier' => $identifier],
			function($message) use ($user)
			{
				$message->to($user->email, $user->name);
				$message->subject('Password Recovery');
			}
		);

		$this->flash->addMessage('info', 'A password recovery email has been sent.');

		return $response->withRedirect($this->router->pathFor('home'));
	}

	private function invalidResetPasswordAttempt($user, $hashedIdentifier)
	{
		return !$user || !$user->recover_hash || !$this->HashUtil->checkHash($user->recover_hash, $hashedIdentifier);
	}

	public function getResetPassword($request, $response)
	{
		$email = $request->getParam('email');
		$identifier = $request->getParam('identifier');

		$hashedIdentifier = $this->HashUtil->hash($identifier);

		$user = User::getUser($email);

		if ( $this->invalidResetPasswordAttempt($user, $hashedIdentifier))
			return $response->withRedirect($this->router->pathFor('home'));

		return $this->view->render($response, 'auth/password/resetpassword.twig', [
			'email' => $email,
			'identifier' => $identifier
		]);
	}

	public function postResetPassword($request, $response)
	{
		$email = $request->getParam('email');
		$identifier = $request->getParam('identifier');

		$password = $request->getParam('password');

		$hashedIdentifier = $this->HashUtil->hash($identifier);

		$user = User::getUser($email);

		if ( $this->invalidResetPasswordAttempt($user, $hashedIdentifier))
			return $response->withRedirect($this->router->pathFor('home'));

		$validation = $this->validator->validate($request, [
			'password' => v::noWhitespace()->notEmpty(),
			'password_confirm' => v::confirmedPasswordMatches($password)
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.reset', [], [
				'email' => $email,
				'identifier' => $identifier
			]));

		$user->setPassword($password);
		$user->removePasswordRecoveryHash();

		$this->flash->addMessage('info', 'Password successfully set. You can now sign in.');

		return $response->withRedirect($this->router->pathFor('home'));
	}
}