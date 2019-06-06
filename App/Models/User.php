<?php

namespace App\Models;

class User extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */
	
	protected static function getTableName()
	{
		return 'Users';
	}
	
	protected static function getPrimaryKey()
	{
		return 'UserId';
	}
	
	protected static function getDefaults()
	{
		return [
		];
	}


	/* == Instance Variables == */

	private $userPermissions;
	private $userDetails;


	/* == Creators & Retrievers == */

	public static function createInactiveUser($username, $email, $passwordHash, $identifier)
	{
		return self::createModelWithEntries([
			'Username' => $username,
			'Email' => $email,
			'Password' => $passwordHash,
			'Active' => false,
			'ActiveHash' => $identifier
		]);
	}

	public static function retrieveUserByUserId($userId)
	{
		return self::retrieveModelWithEntries(['UserId' => $userId]);
	}

	public static function retrieveUserByUsername($username)
	{
		return self::retrieveModelWithEntries(['Username' => $username]);
	}

	public static function retrieveUserByEmail($email)
	{
		return self::retrieveModelWithEntries(['Email' => $email]);
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
		return self::retrieveModelWithEntries(['RememberMeIdentifier' => $identifier]);
	}

	public static function retrieveInactiveUserByEmail($email)
	{
		return self::retrieveModelWithEntries([
			'Email' => $email,
			'Active' => false
		]);
	}


	/* == Getters & Setters == */

	public function getUserId()
	{
		return $this->get('UserId');
	}

	public function getUsername()
	{
		return $this->get('Username');
	}

	public function setUsername($username)
	{
		$this->set('Username', $username);
	}

	public function getEmail()
	{
		return $this->get('Email');
	}

	public function setEmail($email)
	{
		$this->set('Email', $email);
	}

	public function getPasswordHash()
	{
		return $this->get('Password');
	}

	public function setHashedPassword($password)
	{
		$this->set('Password', $password);
	}

	public function setUnhashedPassword($password)
	{
		$this->setHashedPassword(password_hash($password, PASSWORD_DEFAULT));
	}

	public function isActive()
	{
		return (bool) $this->get('Active');
	}

	public function getActiveHash()
	{
		return $this->get('ActiveHash');
	}

	public function activateAccount()
	{
		$this->update([
			'Active' => true,
			'ActiveHash' => null
		]);
	}

	public function getPasswordRecoveryHash()
	{
		return $this->get('RecoveryHash');
	}

	public function setPasswordRecoveryHash($recoveryHash)
	{
		$this->set('RecoveryHash', $recoveryHash);
	}

	public function getRememberMeIdentifier()
	{
		return $this->get('RememberMeIdentifier');
	}

	public function getRememberMeToken()
	{
		return $this->get('RememberMeToken');
	}

	public function setRememberMeCredentials($identifier, $token)
	{
		$this->update([
			'RememberMeIdentifier' => $identifier,
			'RememberMeToken' => $token
		]);
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
		    $this->userDetails = UserDetails::retrieveUserDetailsByUserId($this->getUserId());
		return $this->userDetails;
	}

	public function getPreferredName()
	{
		return $this->getUserDetails()->getPreferredName();
	}

	/* == Miscellaneous == */

	public function removeRememberMeCredentials()
	{
		$this->setRememberMeCredentials(null, null);
	}

	public function removePasswordRecoveryHash()
	{
		$this->setPasswordRecoveryHash(null);
	}
}