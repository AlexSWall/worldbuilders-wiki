<?php

$baseUrl = '';
$db_name = '';
$db_username = '';
$db_password = '';
$email = '';
$email_password = '';
$name = '';

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
		'logging' => true /* TODO */
	],
	'loggers' => [
		/* Loggers: setup, general, network, database, security */
		/* Could be expanded to include: business, statistics, suspicious-activity, ... */
		'setup' => [
			'logger_name' => 'setup_logger',
			'channel_name' => 'setup',
			'level' => 100,
			'path' => '/logs/app.log'
		],
		'general' => [  /* The default logger */
			'logger_name' => 'gen_logger',
			'channel_name' => 'general',
			'level' => 100,
			'path' => '/logs/app.log'
		],
		'network' => [
			'logger_name' => 'net_logger',
			'channel_name' => 'network',
			'level' => 100,
			'path' => '/logs/app.log'
		],
		'database' => [
			'logger_name' => 'db_logger',
			'channel_name' => 'database',
			'level' => 100,
			'path' => '/logs/app.log'
		],
		'security' => [
			'logger_name' => 'sec_logger',
			'channel_name' => 'security',
			'level' => 100,
			'path' => '/logs/app.log'
		],
		'debug' => [
			'logger_name' => 'debug_logger',
			'channel_name' => 'debug',
			'level' => 100,
			'path' => '/logs/app.log'
		]
	],
	'db' => [
		'driver' => 'mysql',
		'host' => 'mysql',
		'database' => $db_name,
		'username' => $db_username,
		'password' => $db_password,
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => ''
	],
	'auth' => [
		'userId' => 'user_id',
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
