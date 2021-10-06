<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use App\Models\Character;
use App\Globals\GlobalsFacade;

use Slim\Http\Response;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Carbon\Carbon;

class Auth
{
	static $logger;

	protected array $authConfig;

	protected \App\Helpers\HashingUtilities $hashUtilities;
	protected \RandomLib\Generator $generator;

	public function __construct( array $authConfig, \App\Helpers\HashingUtilities $hashUtilities, \RandomLib\Generator $generator )
	{
		$this->authConfig = $authConfig;
		$this->hashUtilities = $hashUtilities;
		$this->generator = $generator;
	}

	public function checkUserExists( string $identity ): bool
	{
		return User::retrieveUserByIdentity( $identity ) !== null;
	}

	public function checkActivated( string $identity ): bool
	{
		return User::retrieveUserByIdentity( $identity )->isActive();
	}

	public function attempt( string $identity, string $password ): bool
	{
		$user = User::retrieveUserByIdentity( $identity );

		if ( !$user )
		{
			return false;
		}

		if ( !$this->hashUtilities->checkPassword( $password, $user->getPasswordHash() ) )
		{
			return false;
		}

		$_SESSION[ $this->authConfig[ 'session' ] ] = $user->getUserId();

		$characters = Character::retrieveCharactersByUserId( $user->getUserId() );
		if ( $characters )
		{
			$_SESSION[ $this->authConfig[ 'characterId' ] ] = $characters[0]->getCharacterId();
		}

		return true;
	}

	private function createRememberMeCookie( string $value, string $expiry_str ): SetCookie
	{
		return SetCookie::create( $this->authConfig[ 'remember' ] )
			->withValue( $value )
			->withExpires( Carbon::parse( $expiry_str )->timestamp )
			->withPath( '/' );
	}

	public function setRememberMeCookie( Response $response, string $identity ): Response
	{
		$user = User::retrieveUserByIdentity( $identity );

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

	public function isAuthenticated(): bool
	{
		return isset( $_SESSION[ $this->authConfig[ 'session' ] ] );
	}

	public function getUser(): User
	{
		return User::retrieveUserByUserId( $_SESSION[ $this->authConfig[ 'session' ] ] );
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
		$characterIdKey = $this->authConfig[ 'characterId' ];
		if ( array_key_exists( $characterIdKey, $_SESSION ) && $characterId = $_SESSION[ $characterIdKey ] )
		{
			self::$logger->info('Found character, returning it');

			$character = Character::retrieveCharacterByCharacterId( $characterId );
		}
		else
		{
			self::$logger->info('Character ID key not present in $_SESSION');
			self::$logger->dump($_SESSION);
		}

		if ( $user && $character && $user->getUserId() === $character->getUserId() )
		{
			self::$logger->info('Found character, returning it');

			return $character;
		}
		else
		{
			self::$logger->info('Failed to get character');

			return null;
		}
	}

	public function getUserSafely(): ?User
	{
		if ( $this->isAuthenticated() )
		{
			return User::retrieveUserByUserId( $_SESSION[ $this->authConfig[ 'session' ] ] );
		}

		return null;
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

		unset( $_SESSION[ $this->authConfig[ 'session' ] ] );

		return $response;
	}
}
