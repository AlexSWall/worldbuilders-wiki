<?php declare(strict_types=1);

namespace App\Validation;

use App\Models\User;

class Rules
{
	static $logger;

	// == Core Rules (Callables) ==

	public static function required( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Required";

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( ! $input )
				return $failureString;
			return null;
		};
	}

	public static function length( int $minLength, int $maxLength, ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be between {$minLength} and {$maxLength} characters long";

		return function( string $input ) use ( $minLength, $maxLength, $failureString ) : ?string
		{
			$inputLen = strlen( $input );
			if ( $inputLen < $minLength || $inputLen > $maxLength )
				return $failureString;
			return null;
		};
	}

	public static function alphaAndSpaces( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be only letters and spaces" ;

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( ! preg_match( '/^[a-zA-Z ]*$/', $input ) )
				return $failureString;
			return null;
		};
	}

	public static function alphaNumeric( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be only letters and numbers" ;

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( ! ctype_alnum( $input ) )
				return $failureString;
			return null;
		};
	}

	public static function email( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be a valid email address" ;

		return function( string $input ) use ( $failureString ) : ?string
		{
			// TODO: Add more precise email validation via EmailValidator
			if ( ! filter_var( $input, FILTER_VALIDATE_EMAIL ) )
				return $failureString;
			return null;
		};
	}

	public static function usernameAvailable( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Username is already in use" ;

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( ! is_null( User::retrieveUserByUsername( $input ) ) )
				return $failureString;
			return null;
		};
	}

	public static function emailAvailable( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Email is already in use";

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( ! is_null( User::retrieveUserByEmail( $input ) ) )
				return $failureString;
			return null;
		};
	}

	public static function emailInUse( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Email is not in use";

		return function( string $input ) use ( $failureString ) : ?string
		{
			if ( is_null( User::retrieveUserByEmail( $input ) ) )
				return $failureString;
			return null;
		};
	}

	public static function matchesPassword( string $passwordHash, ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Incorrect password";

		return function( string $input ) use ( $passwordHash, $failureString ) : ?string
		{
			if ( ! password_verify( $input, $passwordHash ) )
				return $failureString;
			return null;
		};
	}

	// == Derived Rules (Arrays) ==

	public static function usernameRules() : array
	{
		return [ self::required(), self::alphaNumeric(), self::length(4, 20) ];
	}

	public static function emailRules() : array
	{
		return [ self::required(), self::email() ];
	}

	public static function passwordRules() : array
	{
		return [ self::required(), self::length(6, 30) ];
	}

	public static function usernameAvailableRules() : array
	{
		return array_merge( self::usernameRules(), [ self::usernameAvailable() ] );
	}

	public static function emailAvailableRules() : array
	{
		return array_merge( self::emailRules(), [ self::emailAvailable() ] );
	}

	public static function emailInUseRules() : array
	{
		return array_merge( self::emailRules(), [ self::emailInUse() ] );
	}

	public static function passwordCorrectRules( $passwordHash ) : array
	{
		return [ self::required(), self::matchesPassword( $passwordHash ) ];
	}

	public static function preferredNameRules()
	{
		return [ self::alphaAndSpaces(), self::length(0, 20) ];
	}
}
