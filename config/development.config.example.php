<?php
return [
	'displayErrorDetails' => true,
	'addContentLengthHeader' => false,
	'app' => [
		'url' => 'http://localhost',
		'hash' => [
			'algo' => PASSWORD_BCRYPT,
			'cost' => 10
		]
	],
	'logger' => [
		'name' => 'channel name',
		'level' => Monolog\Logger::DEBUG,
		'path' => BASE_PATH . '/relative/path.log'
	],
	'db' => [
		'driver' => 'mysql',
		'host' => '127.0.0.1',
		'database' => 'database',
		'username' => 'username',
		'password' => 'password',
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
		'username' => 'email address',
		'password' => 'password',
		'html' => true,
		'from_email' => 'email address',
		'from_name' => 'name'
	],
	'twig' => [
		'debug' => true
	],
	'csrf' => [
		'session' => 'csrf_token'
	]
];