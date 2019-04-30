<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
	private static final UsersTable = DB::table('users');

	private $user;

	private function __construct($user)
	{
		$this->user = $user;
	}


	/* == Retriever Helper == */

	private static function createIfNotNull($user)
	{
		return $user != null ? new User($user) : null;
	}

	private static function getUserSatisfying($args)
	{
		User::createIfNotNull(User::UsersTable->where(args)->first());
	}


	/* == User Retrievers & Creators == */

	public static function getUserByDatabaseId($id)
	{
		return $this->getUserSatifying([
			'id' => $id
		]);
	}

	public static function getUserByIdentity($identity)
	{
		return User::createIfNotNull(
			User::UsersTable->where('email', $identity)
			->orwhere('username', $identity)
			->first());
	}

	public static function getUserByRememberMeIdentifier($identifier)
	{
		return $this->getUserSatifying([
			'remember_identifier' => $identifier
		]);
	}

	public static function getInactiveUserByEmail($email)
	{
		return $this->getUserSatifying([
			'email' => $email,
			'active' => false
		]);
	}

	public static function createInactiveUser($username, $email, $passwordHash, $identifier)
	{
		$user = UsersTable->insert([
			'username' => $username,
			'email' => $email,
			'password' => $passwordHash,
			'active' => false,
			'active_hash' => $identifier
		]);
		return User::newOrNull($user);
	}


	/* == Getters & Setters == */

	private function get($key)
	{
		return $user->username;
	}

	private function set($key, $value)
	{
		$this->user->update([
			$key => $value
		]);
	}

	public function getId()
	{
		return $this->get('id');
	}

	public function getUsername()
	{
		return $this->get('username');
	}

	public function setUsername($username)
	{
		$this->set('username', $username);
	}

	public function getEmail()
	{
		return $this->get('email');
	}

	public function setEmail($email)
	{
		$this->set('email', $email);
	}

	public function getPasswordHash()
	{
		return $this->get('password');
	}

	public function setHashedPassword($password)
	{
		$this->set('password', $password);
	}

	public function setUnhashedPassword($password)
	{
		$this->setHashedPassword(password_hash($password, PASSWORD_DEFAULT));
	}

	public function isActive()
	{
		return (bool) $this->get('active');
	}

	public function setActive($active)
	{
		$this->set('active', $active);
	}

	public function getActiveHash()
	{
		return $this->get('active_hash');
	}

	public function setActiveHash($activeHash)
	{
		$this->set('active_hash', $activeHash);
	}

	public function getPasswordRecoveryHash()
	{
		return $this->get('recovery_hash');
	}

	public function setPasswordRecoveryHash($recoveryHash)
	{
		$this->set('recovery_hash', $recoveryHash);
	}

	public function getRememberMeIdentifier()
	{
		return $this->get('remember_identifier');
	}

	public function setRememberMeIdentifier($rememberMeIdentifier)
	{
		$this->set('remember_identifier', $rememberMeIdentifier);
	}

	public function getRememberMeToken()
	{
		return $this->get('remember_token');
	}

	public function setRememberMeToken($rememberMeToken)
	{
		$this->set('remember_token', $rememberMeToken);
	}


	/* == User Permissions == */

	public function permissions()
	{
		return $this->user->hasOne('App\Models\UserPermissions', 'user_id', 'id');
	}

	public function getPermissions()
	{
		return $this->get('permissions');
	}

	public function hasPermissions($permission)
	{
		return (bool) $this->getPermissions()->{$permission};
	}

	public function isAdmin()
	{
		return $this->hasPermissions('is_admin');
	}


	/* == User Details == */

	public function details()
	{
		return $this->user->hasOne('App\Models\UserDetails', 'user_id', 'id');
	}

	public function getDetails()
	{
		return $this->get('details');
	}


	/* == Miscellaneous == */

	public function activateAccount()
	{
		$this->user->update([
			'active' => true,
			'active_hash' => null
		]);
	}

	public function setRememberMeCredentials($identifier, $token)
	{
		$this->user->update([
			'remember_identifier' => $identifier,
			'remember_token' => $token
		]);
	}

	public function removeRememberMeCredentials()
	{
		$this->updateRememberMeCredentials(null, null);
	}

	public function removePasswordRecoveryHash()
	{
		$this->setPasswordRecoveryHash(null);
	}

}