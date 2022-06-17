<?php

declare(strict_types=1);

namespace App\Models;

class User extends DatabaseEncapsulator
{
	/* == Required Abstract Methods == */

	protected static function getTableName(): string
	{
		return 'Users';
	}

	protected static function getPrimaryKey(): string
	{
		return 'UserId';
	}

	protected static function getDefaults(): array
	{
		return [];
	}


	/* == Instance Variables == */

	private ?UserPermissions $userPermissions = null;
	private ?UserDetails $userDetails = null;


	/* == Creators & Retrievers == */

	public static function createInactiveUser( string $username, string $email, string $passwordHash, string $identifier ): ?User
	{
		return self::createModelWithEntries( [
			'Username' => $username,
			'Email' => $email,
			'Password' => $passwordHash,
			'Active' => false,
			'ActiveHash' => $identifier
		] );
	}

	public static function retrieveUserByUserId( int $userId ): ?User
	{
		return self::retrieveModelWithEntries( ['UserId' => $userId] );
	}

	public static function retrieveUserByUsername( string $username ): ?User
	{
		return self::retrieveModelWithEntries( ['Username' => $username] );
	}

	public static function retrieveUserByEmail( string $email ): ?User
	{
		return self::retrieveModelWithEntries( ['Email' => $email] );
	}

	public static function retrieveUserByIdentity( string $identity ): ?User
	{
		if ( str_contains( $identity, '@' ) )
		{
			$user = self::retrieveUserByEmail( $identity );
		}

		if ( !$user )
		{
			$user = self::retrieveUserByUsername( $identity );
		}

		return $user;
	}

	public static function retrieveUserByRememberMeIdentifier( string $identifier ): ?User
	{
		return self::retrieveModelWithEntries( ['RememberMeIdentifier' => $identifier] );
	}

	public static function retrieveInactiveUserByEmail( string $email ): ?User
	{
		return self::retrieveModelWithEntries( [
			'Email' => $email,
			'Active' => false
		] );
	}


	/* == Getters & Setters == */

	public function getUserId(): int
	{
		return $this->get( 'UserId' );
	}

	public function getUsername(): string
	{
		return $this->get( 'Username' );
	}

	public function setUsername( string $username ): void
	{
		$this->set( 'Username', $username );
	}

	public function getEmail()
	{
		return $this->get( 'Email' );
	}

	public function setEmail( string $email ): void
	{
		$this->set( 'Email', $email );
	}

	public function getPasswordHash()
	{
		return $this->get( 'Password' );
	}

	public function setHashedPassword( string $password ): void
	{
		$this->set( 'Password', $password );
	}

	public function setUnhashedPassword( string $password ): void
	{
		$hashingUtilities = \App\Globals\GlobalsFacade::getHashingUtilities();
		$this->setHashedPassword( $hashingUtilities->hashPassword( $password ) );
	}

	public function isActive(): bool
	{
		return (bool) $this->get( 'Active' );
	}

	public function getActiveHash(): ?string
	{
		return $this->get( 'ActiveHash' );
	}

	public function activateAccount(): void
	{
		$this->update( [
			'Active' => true,
			'ActiveHash' => null
		] );
	}

	public function getPasswordRecoveryHash(): ?string
	{
		return $this->get( 'RecoveryHash' );
	}

	public function setPasswordRecoveryHash( ?string $recoveryHash ): void
	{
		$this->set( 'RecoveryHash', $recoveryHash );
	}

	public function getRememberMeIdentifier(): ?string
	{
		return $this->get( 'RememberMeIdentifier' );
	}

	public function getRememberMeToken(): ?string
	{
		return $this->get( 'RememberMeToken' );
	}

	public function setRememberMeCredentials( ?string $identifier, ?string $token ): void
	{
		$this->update( [
			'RememberMeIdentifier' => $identifier,
			'RememberMeToken' => $token
		] );
	}

	/* == User Permissions == */

	public function createUserPermissions(): void
	{
		UserPermissions::createDefaultUserPermissions( $this->getUserId() );
	}

	public function getUserPermissions(): UserPermissions
	{
		/* Lazy instantiation. */
		if ( !$this->userPermissions )
		{
			$this->userPermissions = UserPermissions::retrieveUserPermissionsByUserId( $this->getUserId() );
		}
		return $this->userPermissions;
	}

	public function isAdmin(): bool
	{
		return $this->getUserPermissions()->isAdmin();
	}


	/* == User Details == */

	public function createUserDetails( string $preferredName ): void
	{
		UserDetails::createUserDetails( $this->getUserId(), $preferredName );
	}

	public function getUserDetails(): UserDetails
	{
	    /* Lazy instantiation. */
		if ( !$this->userDetails )
		{
		    $this->userDetails = UserDetails::retrieveUserDetailsByUserId( $this->getUserId() );
		}
		return $this->userDetails;
	}

	public function getPreferredName(): string
	{
		return $this->getUserDetails()->getPreferredName();
	}

	/* == Miscellaneous == */

	public function removeRememberMeCredentials(): void
	{
		$this->setRememberMeCredentials( null, null );
	}

	public function removePasswordRecoveryHash(): void
	{
		$this->setPasswordRecoveryHash( null );
	}
}
