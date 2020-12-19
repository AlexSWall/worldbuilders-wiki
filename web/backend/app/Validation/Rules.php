<?php declare(strict_types=1);

namespace App\Validation;

class Rules
{
	// == Core Rules (Callables) ==

	public static function required( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Required";

		return function( string $input ) : ?string
		{
			if ( ! $input )
				return $failureString;
		};
	}

	public static function length( int $minLength, int $maxLength, ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be between {$minLength} and {$maxLength} characters long";

		return function( string $input ) : ?string
		{
			$inputLen = strlen( $input );
			if ( $inputLen < $minLength || $inputLen > $maxLength )
				return $failureString;
		};
	}

	public static function alphaAndSpaces( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be only letters and spaces" ;

		return function( string $input ) : ?string
		{
			if ( ! preg_match( '/^[a-zA-Z ]*$/' ) )
				return $failureString;
		};
	}

	public static function alphaNumeric( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be only letters and numbers" ;

		return function( string $input ) : ?string
		{
			if ( ctype_alnum( $input ) )
				return $failureString;
		};
	}

	public static function email( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Must be a valid email address" ;

		return function( string $input ) : ?string
		{
			// TODO: Add more precise email validation via EmailValidator
			if ( ! filter_var( $input, FILTER_VALIDATE_EMAIL ) )
				return $failureString;
		};
	}

	public static function usernameAvailable( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Username is already in use" ;

		return function( string $input ) : ?string
		{
			if ( ! is_null( User::retrieveUserByUsername( $input ) ) )
				return $failureString;
		};
	}

	public static function emailAvailable( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Email is already in use";

		return function( string $input ) : ?string
		{
			if ( ! is_null( User::retrieveUserByEmail( $input ) ) )
				return $failureString;
		};
	}

	public static function emailInUse( ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Email is not in use";

		return function( string $input ) : ?string
		{
			if ( is_null( User::retrieveUserByEmail( $input ) ) )
				return $failureString;
		};
	}

	public static function matchesPassword( string $password, ?string $failureString = null ) : callable
	{
		$failureString = $failureString ?: "Incorrect password";

		return function( string $input ) : ?string
		{
			if ( password_verify( $input, $password ) )
				return $failureString;
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
		return array_merge( self::$USERNAME, [ self::usernameAvailable() ] );
	}

	public static function emailAvailableRules() : array
	{
		return array_merge( self::$EMAIL, [ self::emailAvailable() ] );
	}

	public static function emailInUseRules() : array
	{
		return array_merge( self::$EMAIL, [ self::emailInUse() ] );
	}

	public static function passwordCorrectRules( $password ) : array
	{
		return [ self::required(), self::matchesPassword( $password ) ];
	}

	public static function preferredNameRules()
	{
		return [ self::alphaAndSpaces(), self::length(0, 20) ];
	}
}
