<?php declare( strict_types = 1 );

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\User;
use App\Helpers\DataUtilities;
use App\Helpers\ResponseUtilities;
use App\Helpers\FrontEndDataUtilities;
use App\Validation\Validator;
use App\Validation\Rules;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class AuthenticationController extends Controller
{
	static \App\Logging\Logger $logger;
	static \App\Logging\Logger $debug_logger;

	public function servePostRequest(Request $request, Response $response)
	{
		$parsedBody = $request->getParsedBody();
		$action = $parsedBody['action'];
		$data = $parsedBody['data'];

		self::$logger->info('Received auth POST request with action \'' . $action . '\'');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		// -- Validate --

		if (!DataUtilities::isNonEmptyString($action))
			return $errorResponse(400, "'action' must be a non-empty string");

		if (!is_array($data))
			return $errorResponse(400, "'data' must be a JSON object/array");

		// -- Act --

		// The following code is at least one of: brilliant, mad, and disgusting.

		$apiActionData = self::getAPIActionData($action);

		if ( $apiActionData !== null )
		{
			[ $handler, $argsData ] = $apiActionData;

			$args = [];

			foreach ( $argsData as $key => $validator )
			{
				// Get required argument from API data key's value.
				$arg = $data[$key];

				// Get whether validation returns a requirement string.
				$validationRequirement = $validator( $arg );
				if ( $validationRequirement )
					return $errorResponse(400, "'{$action}' action needs data with '{$key}' key and {$validationRequirement} value");

				// No requirement string: validation succeeded, add to list of args
				$args[] = $arg;
			}

			return $this->$handler( $response, ...$args );
		}
		else
		{
			// Special cases
			switch ( $action )
			{
				case 'sign out':
					return $this->signOut($request, $response);

				default:
					return $errorResponse(400, "invalid action");
			}
		}

		// This should be unreachable
		return $errorResponse(500, "server error");
	}

	private static function getAPIActionData( string $action ): ?array
	{
		$createValidator = function ( $validator, string $description ): callable
		{
			return function ( $input ) use ( $validator, $description ): ?string
			{
				return ( ! $validator( $input ) ) ? $description : null;
			};
		};

		$stringCheck = $createValidator( 'is_string', 'string' );
		$nonEmptyStringCheck = $createValidator( '\App\Helpers\DataUtilities::isNonEmptyString', 'non-empty string' );
		$booleanCheck = $createValidator( 'is_bool', 'boolean' );

		switch ( $action )
		{
			case 'sign up':
				return [ 'signUp', [
					'username' => $nonEmptyStringCheck,
					'email' => $nonEmptyStringCheck,
					'password' => $nonEmptyStringCheck,
					'preferred_name' => $stringCheck
				]];

			case 'sign in':
				return [ 'signIn', [
					'identity' => $nonEmptyStringCheck,
					'password' => $nonEmptyStringCheck,
					'remember_me' => $booleanCheck
				]];

			case 'change password':
				return [ 'changePassword', [
					'password_old' => $nonEmptyStringCheck,
					'password_new' => $nonEmptyStringCheck
				]];

			case 'request password reset email':
				return [ 'requestPasswordResetEmail', [
					'email' => $nonEmptyStringCheck,
				]];

			case 'reset password':
				return [ 'resetPassword', [
					'email' => $nonEmptyStringCheck,
					'identifier' => $nonEmptyStringCheck,
					'password_new' => $nonEmptyStringCheck,
				]];

			default:
				return null;
		}
	}

	private function signUp(Response $response, string $username, string $email, string $password, string $preferredName): Response
	{
		self::$logger->info('Attempting to sign up client');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error, $extraErrorData) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error, $extraErrorData);
		};

		$preferredName = trim(preg_replace('/\s+/', ' ', $preferredName));

		$errors = Validator::validate([
			'preferred_name' => [ Rules::preferredNameRules(),     $preferredName ],
			'username'       => [ Rules::usernameAvailableRules(), $username      ],
			'email'          => [ Rules::emailAvailableRules(),    $email         ],
			'password'       => [ Rules::passwordRules(),          $password      ]
		]);

		if ( ! empty($errors) )
			return $errorResponse(400, 'Validation failure', ['validation_errors' => $errors]);

		self::$logger->info('Validation passed, creating inactive user');

		$identifier = $this->container->get('randomlib')->generateString(128);

		$user = User::createInactiveUser(
			$username,
			$email,
			$this->HashingUtilities->hashPassword($password),
			$this->HashingUtilities->hash($identifier)
		);

		$user->createUserPermissions();
		$user->createUserDetails($preferredName);

		/* $this->auth->attempt($user->getEmail(), $request->getParam('password')); */

		$this->mailer->send(
			$user,
			'Thanks for registering!',
			'registered.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		return $response->withStatus(200);
	}

	private function signIn(Response $response, string $identity, string $password, bool $rememberMe): Response
	{
		self::$logger->info('Attempting to sign user in');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$debug_logger->info('Checking that client is not already authenticated...');

		if ( $this->auth->isAuthenticated() )
			return $errorResponse(400, 'Already signed in');

		self::$debug_logger->info('Checking that user exists...');

		if ( ! $this->auth->checkUserExists($identity) )
			return $errorResponse(401, 'Identity not in use');

		self::$debug_logger->info('User exists, checking they\'re activated...');

		if ( !$this->auth->checkActivated($identity) )
			return $errorResponse(401, 'Not activated');

		self::$debug_logger->info('User activated, checking authentication succeeds...');

		if ( !$this->auth->attempt($identity, $password) )
			return $errorResponse(401, 'Identity in use but authentication failed');

		self::$debug_logger->info('User successfully logged in');

		if ( $rememberMe )
			$response = $this->auth->setRememberMeCookie($response, $identity);

		return $response->withStatus(200);
	}

	private function signOut(Request $request, Response $response): Response
	{
		self::$logger->info('Attempting to sign user out');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$debug_logger->info('Checking that client is authenticated...');

		if ( ! $this->auth->isAuthenticated() )
			return $errorResponse(400, 'Not signed in');

		$response = $this->auth->logout($request, $response);

		return $response->withStatus(200);
	}

	private function changePassword(Response $response, string $oldPassword, string $newPassword): Response
	{
		self::$logger->info('Attempting to change user\'s password');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error, $extraErrorData = []) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error, $extraErrorData);
		};

		if ( !$this->auth->isAuthenticated() )
			return $errorResponse(400, 'Not signed in');

		$user = $this->auth->getUser();

		self::$logger->info('Client is authenticated, validating passwords');

		$errors = Validator::validate([
			'password_old' => [ Rules::passwordCorrectRules( $user->getPasswordHash() ), $oldPassword ],
			'password_new' => [ Rules::passwordRules(), $newPassword ]
		]);

		if ( !empty($errors) )
			return $errorResponse(400, 'Validation failure', ['validation_errors' => $errors]);

		self::$logger->info('Validataed passwords, setting new password');

		$user->setUnhashedPassword( $newPassword );

		// TODO
		//$this->flash->addMessage('info', 'Your password has been changed.');

		return $response->withStatus(200);
	}

	private function requestPasswordResetEmail(Response $response, string $email): Response
	{
		self::$logger->info('Attempting to send reset password email');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error, $extraErrorData = []) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error, $extraErrorData);
		};

		self::$logger->info('Validating supplied email address');

		$errors = Validator::validate([
			'email' => [ Rules::emailInUseRules(), $email ]
		]);

		if ( !empty($errors) )
			return $errorResponse(400, 'Validation failure', ['validation_errors' => $errors]);

		self::$logger->info('Validataed email address, creating password recovery email');

		$user = User::retrieveUserByIdentity($email);

		if ( $user === null )
			return $errorResponse(400, 'Failed to retrieve user via email address');

		$identifier = $this->container->get('randomlib')->generateString(128);
		$hashedIdentifier = $this->HashingUtilities->hash($identifier);
		
		$user->setPasswordRecoveryHash($hashedIdentifier);

		self::$logger->info('Sending password recovery email');

		$this->mailer->send(
			$user,
			'Password Recovery',
			'recoverPassword.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		return $response->withStatus(200);
	}

	private function resetPassword(Response $response, string $email, string $identifier, string $newPassword): Response
	{
		self::$logger->info('Attempting to send reset password email');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error, $extraErrorData = []) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error, $extraErrorData);
		};

		self::$logger->info('Attempting to retrieve user by email');

		$user = User::retrieveUserByIdentity($email);

		if ( ! $user )
			return $errorResponse(400, 'Failed to find user');

		if ( ! $user->getPasswordRecoveryHash() )
			return $errorResponse(400, 'Password recovery identifier expired');

		$hashedIdentifier = $this->HashingUtilities->hash($identifier);

		if ( ! $this->HashingUtilities->checkHash( $user->getPasswordRecoveryHash(), $hashedIdentifier ) )
			return $errorResponse(400, 'Incorrect password recovery hash supplied');

		self::$logger->info('Successfully authenticated with password recovery hash, validating new password');

		$errors = Validator::validate([
			'password_new' => [ Rules::passwordRules(), $newPassword ]
		]);

		if ( !empty($errors) )
			return $errorResponse(400, 'Validation failure', ['validation_errors' => $errors]);

		self::$logger->info('New password successfully validated, setting it now');

		$user->setUnhashedPassword( $newPassword );
		$user->removePasswordRecoveryHash();

		// TODO
		// $this->flash->addMessage('info', 'Password successfully set. You can now sign in.');

		return $response->withStatus(200);
	}

	public function serveActivationAttempt(Request $request, Response $response): Response
	{
		self::$logger->info('Attempting to activate account');

		// Convenience function for creating response
		$makeResponse = function($messageType, $message) use ($response)
		{
			// TODO
			//self::$logger->info("Responding with {$messageType} flash message: {$message}");
			//$this->flash->addMessage($messageType, $message);
			return $response->withRedirect($this->router->pathFor('home'));
		};

		$email = $request->getParam('email');
		$identifier = $request->getParam('identifier');

		if ( ! $email )
			return $makeResponse('error', '\'email\' GET parameter required');

		if ( ! $identifier )
			return $makeResponse('error', '\'identifier\' GET parameter required');

		self::$logger->info('Retrieving account');

		$user = User::retrieveInactiveUserByEmail($email);

		if ( ! $user )
			return $makeResponse('error', "Inactivate user with email '{$email}' could not be found");

		self::$logger->info('Account retrieved; checking identifier');

		$hashedIdentifier = $this->HashingUtilities->hash($identifier);

		if ( !$this->HashingUtilities->checkHash($user->getActiveHash(), $hashedIdentifier))
			return $makeResponse('error', "Identifier incorrect");

		self::$logger->info('Identifier validated; activating account');

		$user->activateAccount();

		self::$logger->info('Account activated');

		return $makeResponse('info', "Your account has been activated and you can sign in.");
	}

	public function serveResetPasswordGetRequest(Request $request, Response $response): Response
	{
		self::$logger->info('Serving reset password page');

		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'reset-password');
	}
}
