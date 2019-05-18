<?php

/* Example basic variables for config with gmail server. */
$baseUrl = ''
$db_name = ''
$db_username = ''
$db_password = ''
$email = ''
$email_password = ''
$name = ''

return [
	'displayErrorDetails' => true,
	'addContentLengthHeader' => false,
	'app' => [
		'url' => $baseUrl,
		'hash' => [
			'password_hash_algorithm' => PASSWORD_BCRYPT,
			'cost' => 10,
			'standard_hash_algorithm' => 'sha256'
		],
		'logging' => true
	],
	'loggers' => [
		/* Loggers: setup, general, network, database, security */
		/* Could be expanded to include: business, statistics, suspicious-activity, ... */
		\App\Logging\LoggerRegistry::SETUP_LOGGER => [
			'logger_name' => 'setup_logger',
			'channel_name' => \App\Logging\LoggerRegistry::SETUP_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		],
		\App\Logging\LoggerRegistry::GENERAL_LOGGER => [  /* The default logger */
			'logger_name' => 'gen_logger',
			'channel_name' => \App\Logging\LoggerRegistry::GENERAL_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		],
		\App\Logging\LoggerRegistry::NETWORK_LOGGER => [
			'logger_name' => 'net_logger',
			'channel_name' => \App\Logging\LoggerRegistry::NETWORK_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		],
		\App\Logging\LoggerRegistry::DATABASE_LOGGER => [
			'logger_name' => 'db_logger',
			'channel_name' => \App\Logging\LoggerRegistry::DATABASE_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		],
		\App\Logging\LoggerRegistry::SECURITY_LOGGER => [
			'logger_name' => 'sec_logger',
			'channel_name' => \App\Logging\LoggerRegistry::SECURITY_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		],
		\App\Logging\LoggerRegistry::DEBUG_LOGGER => [
			'logger_name' => 'debug_logger',
			'channel_name' => \App\Logging\LoggerRegistry::DEBUG_LOGGER,
			'level' => Monolog\Logger::DEBUG,
			'path' => BASE_PATH . '/logs/website.log'
		]
	],
	'db' => [
		'driver' => 'mysql',
		'host' => '127.0.0.1',
		'database' => $db_name,
		'username' => $db_username,
		'password' => $db_password,
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => ''
	],
	'auth' => [
		'session' => 'user_id',
		'remember' => 'user_r'
	],
	'mail' => [
		'host' => 'smtp.gmail.com',
		'smtp_auth' => true,
		'smtp_secure' => 'tls',
		'port' => 587,
		'username' => $email,
		'password' => $email_password,
		'html' => true,
		'from_email' => $email,
		'from_name' => $name
	],
	'twig' => [
		'debug' => true
	],
	'csrf' => [
		'session' => 'csrf_token'
	]
];