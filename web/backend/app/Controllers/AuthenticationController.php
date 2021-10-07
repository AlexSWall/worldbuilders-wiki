<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\User;
use App\Helpers\ResponseUtilities;
use App\Helpers\FrontEndDataUtilities;
use App\Validation\Validator;
use App\Validation\Rules;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class AuthenticationController extends Controller
{
	public static \App\Logging\Logger $logger;
	public static \App\Logging\Logger $debug_logger;

	public function servePostRequest( Request $request, Response $response )
	{
		$stringCheck = \App\Utilities\APIUtilities::$isStringValidator;
		$nonEmptyStringCheck = \App\Utilities\APIUtilities::$isNonEmptyStringValidator;
		$booleanCheck = \App\Utilities\APIUtilities::$isBooleanValidator;

		$actionStructures = [
			'sign up' => [
				[ $this, 'signUp' ],
				[
					'username' => $nonEmptyStringCheck,
					'email' => $nonEmptyStringCheck,
					'password' => $nonEmptyStringCheck,
					'preferred_name' => $stringCheck
				]
			],
			'sign in' => [
				[ $this, 'signIn' ],
				[
					'identity' => $nonEmptyStringCheck,
					'password' => $nonEmptyStringCheck,
					'remember_me' => $booleanCheck
				]
			],
			'sign out' => [
				[ $this, 'signOut' ],
				[]
			],
			'change password' => [
				[ $this, 'changePassword' ],
				[
					'password_old' => $nonEmptyStringCheck,
					'password_new' => $nonEmptyStringCheck
				]
			],
			'request password reset email' => [
				[ $this, 'requestPasswordResetEmail' ],
				[
					'email' => $nonEmptyStringCheck,
				]
			],
			'reset password' => [
				[ $this, 'resetPassword' ],
				[
					'email' => $nonEmptyStringCheck,
					'identifier' => $nonEmptyStringCheck,
					'password_new' => $nonEmptyStringCheck,
				]
			]
		];

		$entryFunc = \App\Utilities\APIUtilities::createPostAPIEntryPoint(
			'Authentication',
			$actionStructures
		);

		return $entryFunc( $request, $response );
	}

	public function signUp( Response $response, string $username, string $email, string $password, string $preferredName ): Response
	{
		self::$logger->info( 'Attempting to sign up client' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		$preferredName = trim( preg_replace( '/\s+/', ' ', $preferredName ) );

		$errors = Validator::validate( [
			'preferred_name' => [ Rules::preferredNameRules(),     $preferredName ],
			'username'       => [ Rules::usernameAvailableRules(), $username      ],
			'email'          => [ Rules::emailAvailableRules(),    $email         ],
			'password'       => [ Rules::passwordRules(),          $password      ]
		] );

		if ( ! empty( $errors ) )
		{
			return $errorResponse( 400, 'Validation failure', ['validation_errors' => $errors] );
		}

		self::$logger->info( 'Validation passed, creating inactive user' );

		$identifier = $this->container->get( 'randomlib' )->generateString( 128 );

		$user = User::createInactiveUser(
			$username,
			$email,
			$this->HashingUtilities->hashPassword( $password ),
			$this->HashingUtilities->hash( $identifier )
		);

		$user->createUserPermissions();
		$user->createUserDetails( $preferredName );

		/* $this->auth->attempt($user->getEmail(), $request->getParam('password')); */

		$this->mailer->send(
			$user,
			'Thanks for registering!',
			'registered.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		return $response->withStatus( 200 );
	}

	public function signIn( Response $response, string $identity, string $password, bool $rememberMe ): Response
	{
		self::$logger->info( 'Attempting to sign user in' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error );
		};

		self::$debug_logger->info( 'Checking that client is not already authenticated...' );

		if ( $this->auth->isAuthenticated() )
		{
			return $errorResponse( 400, 'Already signed in' );
		}

		self::$debug_logger->info( 'Checking that user exists...' );

		$user = User::retrieveUserByIdentity( $identity );

		if ( $user === null )
		{
			return $errorResponse( 401, 'Identity not in use' );
		}

		self::$debug_logger->info( 'User exists, checking they\'re activated...' );

		if ( ! $user->isActive() )
		{
			return $errorResponse( 401, 'Not activated' );
		}

		self::$debug_logger->info( 'User activated, checking authentication succeeds...' );

		if ( !$this->auth->attemptLogin( $user, $password ) )
		{
			return $errorResponse( 401, 'Authentication failed' );
		}

		self::$debug_logger->info( 'User successfully logged in' );

		if ( $rememberMe )
		{
			$response = $this->auth->setRememberMeCookie( $response, $user );
		}

		return $response->withStatus( 200 );
	}

	public function signOut( Response $response ): Response
	{
		self::$logger->info( 'Attempting to sign user out' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error );
		};

		self::$debug_logger->info( 'Checking that client is authenticated...' );

		if ( ! $this->auth->isAuthenticated() )
		{
			return $errorResponse( 400, 'Not signed in' );
		}

		$response = $this->auth->logout( $response );

		return $response->withStatus( 200 );
	}

	public function changePassword( Response $response, string $oldPassword, string $newPassword ): Response
	{
		self::$logger->info( 'Attempting to change user\'s password' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData = [] ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		if ( !$this->auth->isAuthenticated() )
		{
			return $errorResponse( 400, 'Not signed in' );
		}

		$user = $this->auth->getUser();

		if ( ! $user )
		{
			return $errorResponse( 400, 'Authentication failure' );
		}

		self::$logger->info( 'Client is authenticated, validating passwords' );

		$errors = Validator::validate( [
			'password_old' => [ Rules::passwordCorrectRules( $user->getPasswordHash() ), $oldPassword ],
			'password_new' => [ Rules::passwordRules(), $newPassword ]
		] );

		if ( !empty( $errors ) )
		{
			return $errorResponse( 400, 'Validation failure', ['validation_errors' => $errors] );
		}

		self::$logger->info( 'Validataed passwords, setting new password' );

		$user->setUnhashedPassword( $newPassword );

		// TODO
		//$this->flash->addMessage('info', 'Your password has been changed.');

		return $response->withStatus( 200 );
	}

	public function requestPasswordResetEmail( Response $response, string $email ): Response
	{
		self::$logger->info( 'Attempting to send reset password email' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData = [] ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		self::$logger->info( 'Validating supplied email address' );

		$errors = Validator::validate( [
			'email' => [ Rules::emailInUseRules(), $email ]
		] );

		if ( !empty( $errors ) )
		{
			return $errorResponse( 400, 'Validation failure', ['validation_errors' => $errors] );
		}

		self::$logger->info( 'Validataed email address, creating password recovery email' );

		$user = User::retrieveUserByIdentity( $email );

		if ( $user === null )
		{
			return $errorResponse( 400, 'Failed to retrieve user via email address' );
		}

		$identifier = $this->container->get( 'randomlib' )->generateString( 128 );
		$hashedIdentifier = $this->HashingUtilities->hash( $identifier );

		$user->setPasswordRecoveryHash( $hashedIdentifier );

		self::$logger->info( 'Sending password recovery email' );

		$this->mailer->send(
			$user,
			'Password Recovery',
			'recoverPassword.twig',
			['email' => $user->getEmail(), 'identifier' => $identifier]
		);

		return $response->withStatus( 200 );
	}

	public function resetPassword( Response $response, string $email, string $identifier, string $newPassword ): Response
	{
		self::$logger->info( 'Attempting to send reset password email' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData = [] ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		self::$logger->info( 'Attempting to retrieve user by email' );

		$user = User::retrieveUserByIdentity( $email );

		if ( ! $user )
		{
			return $errorResponse( 400, 'Failed to find user' );
		}

		if ( ! $user->getPasswordRecoveryHash() )
		{
			return $errorResponse( 400, 'Password recovery identifier expired' );
		}

		$hashedIdentifier = $this->HashingUtilities->hash( $identifier );

		if ( ! $this->HashingUtilities->checkHash( $user->getPasswordRecoveryHash(), $hashedIdentifier ) )
		{
			return $errorResponse( 400, 'Incorrect password recovery hash supplied' );
		}

		self::$logger->info( 'Successfully authenticated with password recovery hash, validating new password' );

		$errors = Validator::validate( [
			'password_new' => [ Rules::passwordRules(), $newPassword ]
		] );

		if ( !empty( $errors ) )
		{
			return $errorResponse( 400, 'Validation failure', ['validation_errors' => $errors] );
		}

		self::$logger->info( 'New password successfully validated, setting it now' );

		$user->setUnhashedPassword( $newPassword );
		$user->removePasswordRecoveryHash();

		// TODO
		// $this->flash->addMessage('info', 'Password successfully set. You can now sign in.');

		return $response->withStatus( 200 );
	}

	public function serveActivationAttempt( Request $request, Response $response ): Response
	{
		self::$logger->info( 'Attempting to activate account' );

		// Convenience function for creating response
		$makeResponse = function ( $messageType, $message ) use ( $response )
		{
			// TODO
			//self::$logger->info("Responding with {$messageType} flash message: {$message}");
			//$this->flash->addMessage($messageType, $message);
			return $response->withRedirect( $this->router->pathFor( 'home' ) );
		};

		$email = $request->getParam( 'email' );
		$identifier = $request->getParam( 'identifier' );

		if ( ! $email )
		{
			return $makeResponse( 'error', '\'email\' GET parameter required' );
		}

		if ( ! $identifier )
		{
			return $makeResponse( 'error', '\'identifier\' GET parameter required' );
		}

		self::$logger->info( 'Retrieving account' );

		$user = User::retrieveInactiveUserByEmail( $email );

		if ( ! $user )
		{
			return $makeResponse( 'error', "Inactivate user with email '{$email}' could not be found" );
		}

		self::$logger->info( 'Account retrieved; checking identifier' );

		$hashedIdentifier = $this->HashingUtilities->hash( $identifier );

		if ( !$this->HashingUtilities->checkHash( $user->getActiveHash(), $hashedIdentifier ) )
		{
			return $makeResponse( 'error', "Identifier incorrect" );
		}

		self::$logger->info( 'Identifier validated; activating account' );

		$user->activateAccount();

		self::$logger->info( 'Account activated' );

		return $makeResponse( 'info', "Your account has been activated and you can sign in." );
	}

	public function serveResetPasswordGetRequest( Request $request, Response $response ): Response
	{
		self::$logger->info( 'Serving reset password page' );

		return FrontEndDataUtilities::getEntryPointResponse( $this->view, $response, 'reset-password' );
	}
}
