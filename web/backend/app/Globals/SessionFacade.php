<?php

declare(strict_types=1);

namespace App\Globals;

class SessionFacade
{
	public static \App\Logging\Logger $logger;

	private static ?array $authConfig = null;

	private static array $validKeys = [ 'userId', 'characterId' ];

	public static function initializeSessionFacade( array $authConfig ): void
	{
		if ( $authConfig === null )
		{
			self::$authConfig = $authConfig;
		}
		else
		{
			throw new \InvalidArgumentException('Cannot assign auth config for SESSION facade; already assigned.');
		}
	}

	private static function setter( string $key, ?mixed $value, bool $auth = false ): void
	{
		self::$logger->info("Called SESSION setter for key '{$key}' and value '{$value}'");

		self::checkValidKey( $key );

		if ( $auth )
		{
			$key = self::convertAuthVariable( $key );
		}

		self::$logger->info("Setting value of SESSION key '{$key}' to be '{$value}'");

		$_SESSION[ $key ] = $value;
	}

	private static function getter( string $key, bool $auth = false ): ?mixed
	{
		self::$logger->info("Getting value of SESSION key '{$key}'");

		self::checkValidKey( $key );

		if ( $auth )
		{
			$key = self::convertAuthVariable( $key );
		}

		if ( ! isset( $_SESSION[ $key ] ) )
		{
			self::$logger->info("Value for SESSION key '{$key}' not set");
			return null;
		}

		$value = $_SESSION[ $key ];

		self::$logger->info("Value for SESSION key '{$key}' is '{$value}'");

		return $value;
	}

	private static function checkValidKey( string $key ): void
	{
		if ( ! array_key_exists( $key, self::$validKeys ) )
		{
			throw new \InvalidArgumentException('Invalid SESSION key specified');
		}
	}

	private static function convertAuthVariable( string $key ): string
	{
		if ( ! isset( self::$authConfig[ $key ] ) )
		{
			throw new \InvalidArgumentException('Converting auth variable but key not found in auth config');
		}

		$newKey = self::$authConfig[ $key ];

		self::$logger->info("Converting auth variable {$key} to {$newKey} before storing in SESSION.");

		return $newKey;
	}

	/* == Setters == */

	public static function setUserId( ?int $userId ): void
	{
		self::setter( 'userId', $userId, true );
	}

	public static function setCharacterId( ?int $characterId ): void
	{
		self::setter( 'characterId', $characterId );
	}

	/* == Getters == */

	public static function getUserId(): ?int
	{
		return self::getter( 'userId', true );
	}

	public static function getCharacterId(): ?int
	{
		return self::getter( 'characterId' );
	}
}
