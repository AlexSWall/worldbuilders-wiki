<?php

namespace App\Models;

class User extends DatabaseEncapsulator
{
	protected static function getDefaults()
	{
		return [
			'preferred_name' => null,
			'description' => null
		];
	}
	
	protected static function getTableName()
	{
		return 'users';
	}
	
	protected static function getPrimaryKey()
	{
		return 'user_id';
	}

	private $userPermissions;
	private $userDetails;


	/* == Creators & Retrievers == */

	public static function createInactiveUser($username, $email, $passwordHash, $identifier)
	{
		return self::createModelWithEntries([
			'username' => $username,
			'email' => $email,
			'password' => $passwordHash,
			'active' => false,
			'active_hash' => $identifier
		]);
	}

	public static function retrieveUserByUserId($userId)
	{
		return self::retrieveModelWithEntries(['user_id' => $userId]);
	}

	public static function retrieveUserByUsername($username)
	{
		return self::retrieveModelWithEntries(['username' => $username]);
	}

	public static function retrieveUserByEmail($email)
	{
		return self::retrieveModelWithEntries(['email' => $email]);
	}

	public static function retrieveUserByIdentity($identity)
	{
		$user = self::retrieveUserByUsername($identity);
		if ( !$user )
			$user = self::retrieveUserByEmail($identity);
		return $user;
	}

	public static function retrieveUserByRememberMeIdentifier($identifier)
	{
		return self::retrieveModelWithEntries(['remember_identifier' => $identifier]);
	}

	public static function retrieveInactiveUserByEmail($email)
	{
		return self::retrieveModelWithEntries([
			'email' => $email,
			'active' => false
		]);
	}


	/* == Getters & Setters == */

	public function getUserId()
	{
		return $this->get('user_id');
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

	public function createUserPermissions()
	{
		UserPermissions::createDefaultUserPermissions($this->getUserId());
	}

	public function getUserPermissions()
	{
		/* Lazy instantiation. */
		if ( !$this->userPermissions )
			$this->userPermissions = UserPermissions::retrieveUserPermissionsByUserId($this->getUserId());
		return $this->userPermissions;
	}

	public function isAdmin()
	{
		return $this->getUserPermissions()->isAdmin();
	}


	/* == User Details == */

	public function createUserDetails($preferredName)
	{
		UserDetails::createUserDetails($this->getUserId(), $preferredName);
	}

	public function getUserDetails()
	{
	    /* Lazy instantiation. */
		if ( !$this->userDetails )
		    $this->userDetails = UserDetails::retrieveUserDetailsById($this->getUserId());
		return $this->userDetails;
	}

	public function getPreferredName()
	{
		return $this->getUserDetails()->getPreferredName();
	}

	/* == Miscellaneous == */

	public function activateAccount()
	{
		$this->update([
			'active' => true,
			'active_hash' => null
		]);
	}

	public function setRememberMeCredentials($identifier, $token)
	{
		$this->update([
			'remember_identifier' => $identifier,
			'remember_token' => $token
		]);
	}

	public function removeRememberMeCredentials()
	{
		$this->setRememberMeCredentials(null, null);
	}

	public function removePasswordRecoveryHash()
	{
		$this->setPasswordRecoveryHash(null);
	}
}