<?php

/* == Load dependencies and get config == */

require BASE_PATH . '/vendor/autoload.php'; /* Load dependencies with composer */

require BASE_PATH . '/App/Logging/LoggerRegistry.php';
require BASE_PATH . '/App/Logging/Logger.php';

$config = require BASE_PATH . '/config/' . file_get_contents(BASE_PATH . '/mode.php') . '.config.php';

/* == Set up logging == */

$logger_names = ['logger' => \App\Logging\LoggerRegistry::GENERAL_LOGGER];

foreach ($config['loggers'] as $logger_ref => $logger_config)
{
	\App\Logging\LoggerRegistry::addLoggerFromConfig($logger_ref, $logger_config);
	$logger_names[$logger_config['logger_name']] = $logger_ref;
}

$logger = \App\Logging\LoggerRegistry::get(\App\Logging\LoggerRegistry::SETUP_LOGGER);

spl_autoload_register( function( $class_name ) use ($logger, $logger_names)
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';
	if ( file_exists($class_path) )
	{
		$logger->addInfo('Including ' . $class_name);
		include $class_path; /* No need for include_once; if it had been included, we wouldn't be here. */

		/* Logging black magic: initialising the static loggers after autoloading classes. */
		foreach ( $logger_names as $logger_name => $logger_ref )
			if ( property_exists($class_name, $logger_name) )
				$class_name::$$logger_name = \App\Logging\LoggerRegistry::get($logger_ref);
	}
} );

$logger->addInfo('');
$logger->addInfo('Setup starting.');

/* == Begin setup == */

session_start();

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

/* == Container Items == */

$logger->addInfo('Populating the container.');

$container['HashUtil']             = function($container) { return new \App\Helpers\HashUtil($container->get('settings')['app']['hash']); };
$container['randomlib']            = function($container) { return (new RandomLib\Factory)->getMediumStrengthGenerator(); };
$container['auth']                 = function($container) { return new \App\Auth\Auth($container->get('settings')['auth'], $container->HashUtil, $container->randomlib); };
$container['validator']            = function($container) { return new \App\Validation\Validator; };
$container['HomeController']       = function($container) { return new \App\Controllers\HomeController($container); };
$container['AuthController']       = function($container) { return new \App\Controllers\Auth\AuthController($container); };
$container['AdminController']      = function($container) { return new \App\Controllers\Auth\AdminController($container); };
$container['PasswordController']   = function($container) { return new \App\Controllers\Auth\PasswordController($container); };
$container['ActivationController'] = function($container) { return new \App\Controllers\Auth\ActivationController($container); };
$container['WikiController']       = function($container) { return new \App\Controllers\WikiController($container); };
$container['csrf']                 = function($container) { return new \Slim\Csrf\Guard; };
$container['flash']                = function($container) { return new \Slim\Flash\Messages; };


$container['view'] = function($container)
{
	$view = new \Slim\Views\Twig( BASE_PATH . '/view', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$check = $container->auth->check();
	$user = $container->auth->userSafe();

	$view->getEnvironment()->addGlobal('auth', [
		'check' => $check,
		'user' => $user
	]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	$view->getEnvironment()->addGlobal('baseUrl', $container->get('settings')['app']['url']);

	return $view;
};


$capsule = new \Illuminate\Database\Capsule\Manager; /* Use database component outside of Laravel. */
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function($container) use ($capsule) { return $capsule; };


$container['mailer'] = function($container)
{
	$mailer = new PHPMailer\PHPMailer\PHPMailer;

	$mailerSettings = $container->get('settings')['mail'];

	$mailer->isSMTP();  
	$mailer->Host = $mailerSettings['host'];
	$mailer->SMTPAuth = $mailerSettings['smtp_auth'];
	$mailer->SMTPSecure = $mailerSettings['smtp_secure'];
	$mailer->Port = $mailerSettings['port'];
	$mailer->Username = $mailerSettings['username'];
	$mailer->Password = $mailerSettings['password'];

	$mailer->setFrom($mailerSettings['from_email'], $mailerSettings['from_name']);

	$mailer->isHTML($mailerSettings['html']);

	return new \App\Mail\Mailer($container, $mailer);
};


/* == Middleware == */

$logger->addInfo('Adding middleware.');

$app->add(new \App\Middleware\LogRequestMiddleware($container));
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\RememberMeMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);


/* == Routes == */

$logger->addInfo('Adding the routes.');

require BASE_PATH . '/App/routes.php';

/* == Miscellaneous == */

$logger->addInfo('Completing all other (miscellaneous) bootstrapping.');

Respect\Validation\Validator::with('App\\Validation\\Rules\\');

$logger->addInfo('Finished running bootstrap/app.php');
