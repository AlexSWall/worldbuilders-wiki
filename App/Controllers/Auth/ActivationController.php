<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

use App\Models\User;

class ActivationController extends Controller
{
	public function attemptActivation($request, $response)
	{
		$identifier = $request->getParam('identifier');
		$hashedIdentifier = $this->HashUtil->hash($identifier);

		$user = User::where([
			'email' => $request->getParam('email'),
			'active' => false
		])->first();

		if ( !$user || !$this->HashUtil->checkHash($user->active_hash, $hashedIdentifier))
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