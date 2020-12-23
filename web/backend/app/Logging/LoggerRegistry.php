<?php

namespace App\Logging;

class LoggerRegistry
{
	const SETUP_LOGGER = 'setup';
	const GENERAL_LOGGER = 'general';
	const NETWORK_LOGGER = 'network';
	const DATABASE_LOGGER = 'database';
	const SECURITY_LOGGER = 'security';
	const DEBUG_LOGGER = 'debug';

	/* Populated by init immediately after class definition. */
	private static $allowedKeys;

	/* Called immediately after class definition to populate $allowedKeys. */
	public static function __init__()
	{
		self::$allowedKeys = (new \ReflectionClass(__CLASS__))->getConstants();
	}

	private static $storedValues = [];

	private static function checkKey(string $key)
	{
		if ( !in_array($key, self::$allowedKeys) )
			throw new \InvalidArgumentException('Invalid key given');
		if ( in_array($key, self::$storedValues) )
			throw new \InvalidArgumentException("Registry's key already set");
	}

	public static function addLogger($name, $logger)
	{
		self::set($name, $logger);
	}

	public static function addLoggerFromConfig($name, $config)
	{
		$logger = new \App\Logging\Logger($config);
		self::set($name, $logger);
	}

	public static function set(string $key, \App\Logging\Logger $value)
	{
		self::checkKey($key); /* Throws on failure */
		self::$storedValues[$key] = $value;
	}

	public static function get(string $key)
	{
		self::checkKey($key); /* Throws on failure */
		return self::$storedValues[$key];
	}
} LoggerRegistry::__init__();
