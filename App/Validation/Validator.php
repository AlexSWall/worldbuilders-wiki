<?php

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

/*
{
   "access-denied":"Access denied.",
   "email_already_exists":"Email is already registered on FANDOM",
   "email_invalid":"Email is not valid",
   "invalid-email":"It seems you have no e-mail associated with your FANDOM account. Please {contactFandomLink}",
   "registration_error":"We cannot complete your registration at this time",
   "username_already_exists":"Username is taken",
   "username_blocked":"You can't request a new password because your username has been blocked.",
   "username_unavailable":"Username is taken or has invalid characters. Username can contain letters and numbers from one alphabet and must include one letter",
   "username_ip_address":"Username must include at least one letter",
   "username_exceeds_max_length":"Username cannot exceed 50 characters",
   "username_illegal_characters":"Username can contain only letters and numbers from one alphabet and must include one letter",
   "username-not-recognized":"Hm, we don't recognize this name. Don't forget usernames are case sensitive.",
   "password_exceeds_max_length":"Password cannot exceed 50 characters",
   "password_equal_name":"Password and username cannot match",
   "passwords_not_match":"Passwords do not match",
   "reset-password-email-sent":"We've already sent a password reminder to this account in the last 24 hours. Please check your email.",
   "user_already_linked":"This FANDOM account is already connected to Facebook",
   "fb_account_already_linked":"This Facebook account is already connected to another FANDOM user account",
   "birthdate_invalid":"Birthdate isn't a valid date",
   "wrong-credentials":"We don't recognize these credentials. Try again or register a new account.",
   "server-error":"There was an error processing your request. Please try again later."
}
*/

class Validator
{
	static $logger;

	protected $errors;

	public function validate($params, array $rules)
	{
		if ( $params instanceof \Slim\Http\Request )
			$params = $params->getParams();

		foreach( $rules as $field => $rule )
		{
			try
			{
				$rule->assert($params[$field]);
			}
			catch( NestedValidationException $e )
			{
				$name = self::getFirstExceptionName($e);

				self::$logger->addInfo('Validation exception occurred: ' . $name . ' in ' . $field . ' field');

				$this->errors[$field] = $name;
			}
		}

		$_SESSION['errors'] = $this->errors;
		return $this;
	}

	public function hasFailed()
	{
		return !empty($this->errors);
	}

	private static function getFirstExceptionName($e)
	{
		$it = $e->getIterator();
		$it->rewind();
		$namespacedClassname = get_class($it->current());

		$firstExceptionClassname = substr(
			$namespacedClassname, 
			strrpos($namespacedClassname, '\\') + 1
		);

		return $firstExceptionClassname;
	}
}