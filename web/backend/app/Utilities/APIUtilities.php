<?php

declare(strict_types=1);

namespace App\Utilities;

use App\Helpers\ResponseUtilities;
use App\Helpers\DataUtilities;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class APIUtilities
{
	public static \App\Logging\Logger $logger;

	public static $isStringValidator;
	public static $isNonEmptyStringValidator;
	public static $isBooleanValidator;

	public static function __initialize()
	{
		self::$isStringValidator = self::createValidator( 'is_string', 'string' );
		self::$isNonEmptyStringValidator = self::createValidator( '\App\Helpers\DataUtilities::isNonEmptyString', 'non-empty string' );
		self::$isBooleanValidator = self::createValidator( 'is_bool', 'boolean' );
	}

	/**
	 * Returns a validator, which returns null on validation and an error string
	 * on validation failure.
	 */
	public static function createValidator( $validationFunc, string $description ): callable
	{
		return function ( $input ) use ( $validationFunc, $description ): ?string
		{
			return ( ! $validationFunc( $input ) ) ? $description : null;
		};
	}

	/**
	 * $actionStructures = [
	 *   'action-name' => [
	 *     'handler function name',
	 *     [
	 *       'data name key' => 'validator function name',
	 *       ...
	 *     ]
	 *   ],
	 *   ...
	 * ]
	 */
	public static function createPostAPIEntryPoint( string $name, array $actionStructures ): callable
	{
		return function ( Request $request, Response $response ) use ( $name, $actionStructures ): Response
		{
			$parsedBody = $request->getParsedBody();
			$action = $parsedBody['action'];
			$data = $parsedBody['data'];

			self::$logger->info( 'Received \'' . $name . '\' POST request with action \'' . $action . '\'' );

			// Convenience wrapper for error response
			$errorResponse = function ( $errorCode, $error ) use ( $response )
			{
				return ResponseUtilities::respondWithError( $response, $errorCode, $error );
			};

			// -- Validate --

			if ( !DataUtilities::isNonEmptyString( $action ) ) {
				return $errorResponse( 400, "'action' must be a non-empty string" );
			}

			if ( !is_array( $data ) ) {
				return $errorResponse( 400, "'data' must be a JSON object/array" );
			}

			// -- Act --

			$apiActionStructure = $actionStructures[$action];

			if ( $apiActionStructure !== null )
			{
				[ $handler, $argsData ] = $apiActionStructure;

				$args = [];

				foreach ( $argsData as $key => $validator )
				{
					// Get required argument from API data key's value.
					$arg = $data[$key];

					// Get whether validation returns a requirement string.
					$validationRequirement = $validator( $arg );
					if ( $validationRequirement ) {
						return $errorResponse( 400, "'{$action}' action needs data with '{$key}' key and {$validationRequirement} value" );
					}

					// No requirement string: validation succeeded, add to list of args
					$args[] = $arg;
				}

				return $handler( $response, ...$args );
			} else {
				return $errorResponse( 400, "Invalid action" );
			}

			// This should be unreachable
			return $errorResponse( 500, "Server error" );
		};
	}
}

APIUtilities::__initialize();
