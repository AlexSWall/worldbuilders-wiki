<?php

namespace App\Auth;

use App\Models\User;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;
use Carbon\Carbon;

class Auth
{
	protected $hashUtil;
	protected $authConfig;
	protected $generator;

	public function __construct($authConfig, $hashUtil, $generator)
	{
		$this->hashUtil = $hashUtil;
		$this->authConfig = $authConfig;
		$this->generator = $generator;
	}

	public function attempt($email, $password)
	{
		$user = User::getUser($email);

		if (!$user)
			return false;

		if ( !$this->hashUtil->checkPassword($password, $user->password) )
			return false;

		$_SESSION[$this->authConfig['session']] = $user->id;
		return true;
	}

	private function createRememberCookie($value, $expiry_str)
	{
		return SetCookie::create($this->authConfig['remember'])
			->withValue($value)
			->withExpires(Carbon::parse($expiry_str)->timestamp)
			->withPath('/');
	}

	public function setRememberCookie($response, $email)
	{
		$user = User::getUser($email);

		$rememberIdentifier = $this->generator->generateString(128);
		$rememberToken      = $this->generator->generateString(128);

		$user->updateRememberCredentials(
			$rememberIdentifier,
			$this->hashUtil->hash($rememberToken)
		);

		$response = FigResponseCookies::set(
			$response, 
			$this->createRememberCookie("{$rememberIdentifier}___{$rememberToken}", '+1 week')
		);

		return $response;
	}

	public function check()
	{
		return isset($_SESSION[$this->authConfig['session']]);
	}

	public function user()
	{
		return User::find($_SESSION[$this->authConfig['session']]);
	}

	public function userSafe()
	{
		if ( $this->check() )
			return User::find($_SESSION[$this->authConfig['session']]);
	}

	public function logout($request, $response)
	{
		$rememberMeCookie = FigRequestCookies::get($request, $this->authConfig['remember']);
		$data = $rememberMeCookie->getValue();

		if ( !is_null($data) )
		{
			$this->user()->removeRememberCredentials();
			$response = FigResponseCookies::set($response, $this->createRememberCookie('', '-1 week'));
		}

		unset($_SESSION[$this->authConfig['session']]);

		return $response;
	}
}