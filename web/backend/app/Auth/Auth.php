<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use App\Models\Character;
use App\Globals\GlobalsFacade;
use App\Globals\SessionFacade;

use Slim\Http\Response;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Carbon\Carbon;

class Auth
{
	public static \App\Logging\Logger $logger;

	protected array $authConfig;

	protected \App\Helpers\HashingUtilities $hashUtilities;
	protected \RandomLib\Generator $generator;

	public function __construct( array $authConfig, \App\Helpers\HashingUtilities $hashUtilities, \RandomLib\Generator $generator )
	{
		$this->authConfig = $authConfig;
		$this->hashUtilities = $hashUtilities;
		$this->generator = $generator;
	}

	public function attemptLogin( User $user, string $password ): bool
	{
		if ( !$this->hashUtilities->checkPassword( $password, $user->getPasswordHash() ) )
		{
			self::$logger->info( 'Failed to authenticate: password does not match the password hash stored' );
			return false;
		}

		self::$logger->info( 'Authenticated; password correct' );

		$this->postAuthenticationLogin( $user );

		return true;
	}

	public function setRememberMeCookie( Response $response, User $user ): Response
	{
		$rememberIdentifier = $this->generator->generateString( 128 );
		$rememberToken      = $this->generator->generateString( 128 );

		$user->setRememberMeCredentials(
			$rememberIdentifier,
			$this->hashUtilities->hash( $rememberToken )
		);

		$response = FigResponseCookies::set(
			$response,
			$this->createRememberMeCookie( "{$rememberIdentifier}___{$rememberToken}", '+10 years' )
		);

		return $response;
	}

	public function attemptLoginFromCookie( string $cookieData ): bool
	{
		$cookieData = explode( '___', $cookieData );

		if ( count( $cookieData ) !== 2 )
		{
			self::$logger->info( '\'Remember me\' cookie contains wrong number of sections.' );

			return false;
		}

		$identifier = $cookieData[0];
		$token = $this->hashUtilities->hash( $cookieData[1] );

		$user = User::retrieveUserByRememberMeIdentifier( $identifier );

		if ( !$user )
		{
			self::$logger->info( 'Failed to retrieve user by \'remember me\' identifier.' );

			return false;
		}

		if ( !$this->hashUtilities->checkHash( $token, $user->getRememberMeToken() ) )
		{
			self::$logger->info( 'Hash of second part of \'remember me\' cookie does not equal user\'s saved \'remember me\' token.' );

			$user->removeRememberMeCredentials();
			return false;
		}

		self::$logger->info( 'Authenticated: Valid \'remember me\' token.' );

		$this->postAuthenticationLogin( $user );

		return true;
	}

	private function postAuthenticationLogin( User $user ): void
	{
		self::$logger->info( 'Running post-authentication' );

		SessionFacade::setUserId( $user->getUserId() );

		$characters = Character::retrieveCharactersByUserId( $user->getUserId() );
		if ( $characters )
		{
			// Set session to be first character initially.
			SessionFacade::setCharacterId( $characters[0]->getCharacterId() );
		}

		self::$logger->info( 'Finished running post-authentication' );
	}

	public function logout( Response $response ): Response
	{
		if ( GlobalsFacade::getHasRememberMeCookie() )
		{
			$user = $this->getUser();
			if ( $user )
			{
				$user->removeRememberMeCredentials();
			}

			$response = FigResponseCookies::set( $response, $this->createRememberMeCookie( '', '-1 week' ) );
		}

		SessionFacade::setUserId( null );

		return $response;
	}

	public function isAuthenticated(): bool
	{
		return SessionFacade::getUserId() !== null;
	}

	public function getUser(): ?User
	{
		$userId = SessionFacade::getUserId();

		if ( $userId === null )
			return null;

		return User::retrieveUserByUserId( $userId );
	}

	public function getCharacter(): ?Character
	{
		if ( ! $this->isAuthenticated() )
		{
			self::$logger->info('Not authenticated, so cannot get character');
			return null;
		}

		self::$logger->info('Attempting to get character');

		$user = $this->getUser();
		$character = null;

		// Find character if possible.
		if ( $characterId = SessionFacade::getCharacterId() )
		{
			self::$logger->info('Found character ID');

			$character = Character::retrieveCharacterByCharacterId( $characterId );
		}

		if ( $user && $character && $user->getUserId() === $character->getUserId() )
		{
			self::$logger->info('Found character (with correct user ID), returning it');

			return $character;
		}
		else
		{
			self::$logger->info('Failed to get character');

			return null;
		}
	}

	/* == Private Helpers == */

	private function createRememberMeCookie( string $value, string $expiry_str ): SetCookie
	{
		return SetCookie::create( $this->authConfig[ 'remember' ] )
			->withValue( $value )
			->withExpires( Carbon::parse( $expiry_str )->timestamp )
			->withPath( '/' );
	}
}
