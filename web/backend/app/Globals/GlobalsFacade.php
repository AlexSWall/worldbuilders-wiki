<?php

declare(strict_types=1);

namespace App\Globals;

use App\Models\User;
use App\Helpers\HashingUtilities;

class GlobalsFacade
{
	public static function createNewGlobalsInstance(): void
	{
		$GLOBALS['Globals'] = new GlobalsContainer();
	}

	private static function setter( string $key, mixed $value ): void
	{
		$GLOBALS['Globals']->_setter( $key, $value );
	}

	private static function getter( string $key ): mixed
	{
		return $GLOBALS['Globals']->_getter( $key );
	}

	/* == Setters == */

	public static function setBaseUrl( string $url ): void
	{
		self::setter( 'baseUrl', $url );
	}

	public static function setIsAuthenticated( bool $value ): void
	{
		self::setter( 'isAuthenticated', $value );
	}

	public static function setHashingUtilities( HashingUtilities $hashingUtilities ): void
	{
		self::setter( 'hashingUtilities', $hashingUtilities );
	}

	private ?HashingUtilities $hashUtilities;
	public static function setCsrfTokens( array $tokens ): void
	{
		self::setter( 'csrfTokens', $tokens );
	}

	public static function setUserData( User $data ): void
	{
		self::setter( 'userData', $data );
	}

	public static function setHasRememberMeCookie( bool $hasRememberMeCookie ): void
	{
		self::setter( 'hasRememberMeCookie', $hasRememberMeCookie );
	}

	/* == Getters == */

	public static function getBaseUrl(): ?string
	{
		return self::getter( 'baseUrl' );
	}

	public static function getIsAuthenticated(): ?bool
	{
		return self::getter( 'isAuthenticated' );
	}

	public static function getHashingUtilities(): ?HashingUtilities
	{
		return self::getter( 'hashingUtilities' );
	}

	public static function getCsrfTokens(): ?array
	{
		return self::getter( 'csrfTokens' );
	}

	public static function getUserData(): ?User
	{
		return self::getter( 'userData' );
	}

	public static function getHasRememberMeCookie(): bool
	{
		return self::getter( 'hasRememberMeCookie' );
	}
}
