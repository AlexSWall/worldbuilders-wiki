<?php

namespace App\Validation;

use Respect\Validation\Validator as v;

class Rules
{
/* == Authentication Rules == */

	/* == Base Rules == */
	public static function getValidator()
	{
		return v::charset('ASCII');
	}

	public static function usernameRules()
	{
		return self::getValidator()->notEmpty()->noWhitespace()->alnum()->length(4,20);
	}

	public static function emailRules()
	{
		return self::getValidator()->notEmpty()->email();
	}

	public static function passwordRules()
	{
		return self::getValidator()->notEmpty()->length(8,null);
	}

	/* == Derived Rules == */

	public static function usernameAvailableRules()
	{
		return Rules::usernameRules()->usernameAvailable();
	}

	public static function emailAvailableRules()
	{
		return Rules::emailRules()->emailAvailable();
	}

	public static function emailInUseRules()
	{
		return Rules::emailRules()->emailInUse();
	}

	public static function passwordConfirmationRules($password)
	{
		return self::getValidator()->notEmpty()->confirmedPasswordMatches($password);
	}

	public static function passwordCorrectRules($password)
	{
		return self::getValidator()->notEmpty()->matchesPassword($password);
	}

/* == Account Rules == */

	public static function preferredNameRules()
	{
		return v::optional(self::getValidator()->alpha()->length(1,30));
	}
}