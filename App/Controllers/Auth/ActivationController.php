<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use App\Models\User;

class ActivationController extends Controller
{
	public function attemptActivation($request, $response)
	{
		$identifier = $request->getParam('identifier');
		$hashedIdentifier = $this->HashUtils->hash($identifier);

		$user = User::retrieveInactiveUserByEmail($request->getParam('email'));

		if ( !$user || !$this->HashUtils->checkHash($user->getActiveHash(), $hashedIdentifier))
		{
			$this->flash->addMessage('info', 'There was a problem activating your account');
			return $response->withRedirect($this->router->pathFor('home'));
		}
		else
		{
			$user->activateAccount();
			$this->flash->addMessage('info', 'Your account has been activated and you can sign in.');
			return $response->withRedirect($this->router->pathFor('home'));
		}
	}
}