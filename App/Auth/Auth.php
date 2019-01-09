<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
	protected $hashUtil;

	public function __construct($hashUtil)
	{
		$this->hashUtil = $hashUtil;
	}

	public function attempt($email, $password)
	{
		$user = User::where('email', $email)
			->where('active', true)
			->first();

		if (!$user)
		{
			return false;
		}

		if ( $this->hashUtil->checkPassword($password, $user->password) )
		{
			$_SESSION['user'] = $user->id;
			return true;
		}
		else
			return false;
	}

	public function check()
	{
		return isset($_SESSION['user']);
	}

	public function user()
	{
		return User::find($_SESSION['user']);
	}

	public function userSafe()
	{
		if ( $this->check() )
			return User::find($_SESSION['user']);
	}

	public function logout()
	{
		unset($_SESSION['user']);
	}
}