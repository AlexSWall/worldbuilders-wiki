<?php declare( strict_types = 1 );

namespace App\Logging;

use \App\Logging\Logger;

class LoggerRegistry
{
	public const SETUP_LOGGER = 'setup';
	public const GENERAL_LOGGER = 'general';
	public const NETWORK_LOGGER = 'network';
	public const DATABASE_LOGGER = 'database';
	public const SECURITY_LOGGER = 'security';
	public const DEBUG_LOGGER = 'debug';

	/* Populated by init immediately after class definition. */
	private static array $allowedKeys;

	/* Called immediately after class definition to populate $allowedKeys. */
	public static function __init__()
	{
		self::$allowedKeys = (new \ReflectionClass(__CLASS__))->getConstants();
	}

	private static array $storedValues = [];

	private static function checkKey(string $key): void
	{
		if ( !in_array($key, self::$allowedKeys) )
			throw new \InvalidArgumentException('Invalid key given');
		if ( in_array($key, self::$storedValues) )
			throw new \InvalidArgumentException("Registry's key already set");
	}

	public static function addLogger(string $name, Logger $logger): void
	{
		self::set($name, $logger);
	}

	public static function addLoggerFromConfig(string $name, array $config): void
	{
		$logger = new Logger($config);
		self::set($name, $logger);
	}

	public static function set(string $key, Logger $value): void
	{
		self::checkKey($key); /* Throws on failure */
		self::$storedValues[$key] = $value;
	}

	public static function get(string $key): Logger
	{
		self::checkKey($key); /* Throws on failure */
		return self::$storedValues[$key];
	}
}

LoggerRegistry::__init__();
