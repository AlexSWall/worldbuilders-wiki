<?php

declare(strict_types=1);

namespace App\Validation;

class Validator
{
	public static \App\Logging\Logger $logger;

	/**
	 * Given a valdiation array, checks whether each value satisfies its rules
	 * array. Returns an array of errors with string values detailing the
	 * first validation error for each validation array key. An empty array
	 * therefore implies successful validation.
	 *
	 * The validationArray parameter is of the form:
	 *   [ ParameterName => [ [ RuleFunctions ], ParameterValue ] ]
	 *
	 * @param validationArray The validation array containingthe parameter
	 * names, rules, and values.
	 * @return An array containing any validation errors, indexed by the
	 * parameter names; an empty array returned implies no validation errors.
	 */
	public static function validate( array $validationArray ): array
	{
		$errors = [];

		foreach ( $validationArray as $field => [ $rules, $value ] )
		{
			assert( is_iterable( $rules ), 'Validation array rules must be an iterable of callables' );

			self::$logger->info( 'Trying validation rules for ' . $field );

			foreach ( $rules as $rule )
			{
				assert( is_callable( $rule ), 'Rules in validation array must be callable' );

				$errorString = $rule( $value );
				if ( $errorString !== null )
				{
					// Validation error found for field; set it as the first
					// validation error and finish attempting validation for this
					// field.
					self::$logger->info( "Failed to validate {$field}; error string: ${errorString}" );
					$errors[$field] = $errorString;
					break;
				}
			}

			self::$logger->info( 'Validated ' . $field );
		}

		return $errors;
	}
}
