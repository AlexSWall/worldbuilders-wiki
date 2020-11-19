<?php

namespace App\Auth;

use App\Models\User;
use App\Models\Character;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;
use Carbon\Carbon;

class Auth
{
	protected $hashUtilities;
	protected $authConfig;
	protected $generator;

	public function __construct($authConfig, $hashUtilities, $generator)
	{
		$this->hashUtilities = $hashUtilities;
		$this->authConfig = $authConfig;
		$this->generator = $generator;
	}

	public function checkUserExists($identity)
	{
		return !is_null(User::retrieveUserByIdentity($identity));
	}

	public function checkActivated($identity)
	{
		return User::retrieveUserByIdentity($identity)->isActive();
	}

	public function attempt($identity, $password)
	{
		$user = User::retrieveUserByIdentity($identity);

		if (!$user)
			return false;

		if ( !$this->hashUtilities->checkPassword($password, $user->getPasswordHash()) )
			return false;

		$_SESSION[$this->authConfig['session']] = $user->getUserId();

		$characters = Character::retrieveCharactersByUserId($user->getUserId());
		if ( $characters )
			$_SESSION[$this->authConfig['characterId']] = $characters[0]->getCharacterId();

		return true;
	}

	private function createRememberMeCookie($value, $expiry_str)
	{
		return SetCookie::create($this->authConfig['remember'])
			->withValue($value)
			->withExpires(Carbon::parse($expiry_str)->timestamp)
			->withPath('/');
	}

	public function setRememberMeCookie($response, $identity)
	{
		$user = User::retrieveUserByIdentity($identity);

		$rememberIdentifier = $this->generator->generateString(128);
		$rememberToken      = $this->generator->generateString(128);

		$user->setRememberMeCredentials(
			$rememberIdentifier,
			$this->hashUtilities->hash($rememberToken)
		);

		$response = FigResponseCookies::set(
			$response,
			$this->createRememberMeCookie("{$rememberIdentifier}___{$rememberToken}", '+1 week')
		);

		return $response;
	}

	public function isAuthenticated()
	{
		return isset($_SESSION[$this->authConfig['session']]);
	}

	public function getUser()
	{
		return User::retrieveUserByUserId($_SESSION[$this->authConfig['session']]);
	}

	public function getCharacter()
	{
		if ( ! $this->isAuthenticated() )
			return null;

		$user = $this->getUser();

		$characterId = $_SESSION[$this->authConfig['characterId']];
		$character = Character::retrieveCharacterByCharacterId($characterId);

		if ( $user && $character && $user->getUserId() === $character->getUserId() )
			return $character;
		else
			return null;
	}

	public function getUserSafely()
	{
		if ( $this->isAuthenticated() )
			return User::retrieveUserByUserId($_SESSION[$this->authConfig['session']]);
	}

	public function logout($request, $response)
	{
		$rememberMeCookie = FigRequestCookies::get($request, $this->authConfig['remember']);
		$data = $rememberMeCookie->getValue();

		if ( !is_null($data) )
		{
			$user = $this->getUser();
			if ($user)
				$user->removeRememberMeCredentials();

			$response = FigResponseCookies::set($response, $this->createRememberMeCookie('', '-1 week'));
		}

		unset($_SESSION[$this->authConfig['session']]);

		return $response;
	}
}