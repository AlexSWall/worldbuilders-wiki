<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use App\Models\User;
use App\Helpers\FormUtils;

use App\Validation\Rules as Rules;

class PasswordController extends Controller
{
	public function getChangePassword($request, $response)
	{
		return FormUtils::getForm($this->view, $response, [
			'title' => 'Change Your Password',
			'formType' => 'Change Password'
		]);
	}

	public function postChangePassword($request, $response)
	{
		if ( !$this->auth->check() )
			return $response->withRedirect($this->router->pathFor('home'));

		$newPassword = $request->getParam('password_new');

		$validation = $this->validator->validate($request, [
			'password_old' => Rules::passwordCorrectRules($this->auth->user()->getPasswordHash()),
			'password_new' => Rules::passwordRules(),
			'password_new_confirm' => Rules::passwordConfirmationRules($newPassword)
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.change'));

		$this->auth->user()->setUnhashedPassword($newPassword);

		$this->flash->addMessage('info', 'Your password has been changed.');
		
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getPasswordRecovery($request, $response)
	{
		return FormUtils::getForm($this->view, $response, [
			'title' => 'Password Recovery',
			'formType' => 'Password Recovery'
		]);
	}

	public function postPasswordRecovery($request, $response)
	{
		$email = $request->getParam('email');

		$validation = $this->validator->validate($request, [
			'email' => Rules::emailInUseRules()
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.recovery'));

		$user = User::retrieveUserByIdentity($email);

		$identifier = $this->container->randomlib->generateString(128);
		$hashedIdentifier = $this->HashUtils->hash($identifier);
		
		$user->setPasswordRecoveryHash($hashedIdentifier);

		$this->mailer->send(
			$user,
			'Password Recovery',
			'recoverPassword.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		return $response->withRedirect($this->router->pathFor('home'));
	}

	private function validResetPasswordAttempt($user, $hashedIdentifier)
	{
		if ( !$user || !$user->getPasswordRecoveryHash() )
			return False;
		return $this->HashUtils->checkHash($user->getPasswordRecoveryHash(), $hashedIdentifier);
	}

	public function getResetPassword($request, $response)
	{
		$email = $request->getParam('email');
		$identifier = $request->getParam('identifier');

		$hashedIdentifier = $this->HashUtils->hash($identifier);

		$user = User::retrieveUserByIdentity($email);

		if ( !$this->validResetPasswordAttempt($user, $hashedIdentifier))
			return $response->withRedirect($this->router->pathFor('home'));

		return FormUtils::getForm($this->view, $response, [
			'title' => 'Reset Your Password',
			'formType' => 'Reset Password',
			'email' => $email,
			'identifier' => urlencode($identifier),
			'oldValues' => ['' => '']
		]);
	}

	public function postResetPassword($request, $response)
	{
		$email = $request->getParam('email');
		$identifier = $request->getParam('identifier');

		$password = $request->getParam('password_new');

		$hashedIdentifier = $this->HashUtils->hash($identifier);

		$user = User::retrieveUserByIdentity($email);

		if ( !$this->validResetPasswordAttempt($user, $hashedIdentifier))
			return $response->withRedirect($this->router->pathFor('home'));

		$validation = $this->validator->validate($request, [
			'password_new' => Rules::passwordRules(),
			'password_new_confirm' => Rules::passwordConfirmationRules($password)
		]);

		if ($validation->hasFailed())
			return $response->withRedirect($this->router->pathFor('auth.password.reset', [], [
				'email' => $email,
				'identifier' => $identifier
			]));

		$user->setUnhashedPassword($password);
		$user->removePasswordRecoveryHash();

		$this->flash->addMessage('info', 'Password successfully set. You can now sign in.');

		return $response->withRedirect($this->router->pathFor('home'));
	}
}