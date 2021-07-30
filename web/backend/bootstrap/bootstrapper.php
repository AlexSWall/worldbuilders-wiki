<?php

$setupFinished = false;  /* For logging purposes. */

/* == Load dependencies == */

assert(isset($config));

if ( ! defined('BASE_PATH') )
	define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php'; /* Load dependencies with composer */

require_once BASE_PATH . '/app/Logging/LoggerRegistry.php';
require_once BASE_PATH . '/app/Logging/Logger.php';

/* == Set up logging == */

$logger_names = ['logger' => \App\Logging\LoggerRegistry::GENERAL_LOGGER];

foreach ($config['loggers'] as $logger_ref => $logger_config)
{
	\App\Logging\LoggerRegistry::addLoggerFromConfig($logger_ref, $logger_config);
	$logger_names[$logger_config['logger_name']] = $logger_ref;
}

$loggers = [];
foreach ( $logger_names as $logger_name => $logger_ref )
	$loggers[$logger_name] = \App\Logging\LoggerRegistry::get($logger_ref);


$logger = \App\Logging\LoggerRegistry::get(\App\Logging\LoggerRegistry::SETUP_LOGGER);

spl_autoload_register( function( $class_name ) use ($logger, $logger_names, &$setupFinished)
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';

	$class_path = str_replace('/App/', '/app/', $class_path);

	if ( file_exists($class_path) )
	{
		if ( $setupFinished )
			$logger->addInfo('Including ' . $class_name);

		include $class_path; /* No need for include_once; if it had been included, we wouldn't be here. */

		/* Logging black magic: initialising the static loggers after autoloading classes. */
		foreach ( $logger_names as $logger_name => $logger_ref )
			if ( property_exists($class_name, $logger_name) )
				$class_name::$$logger_name = \App\Logging\LoggerRegistry::get($logger_ref);
	}
} );

$logger->addInfo('---------------');
$logger->addInfo('Setup starting.');

/* == Begin setup == */

session_start();

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

/* == Container Items == */

$logger->addInfo('Populating the container.');

$container['loggers'] = $loggers;

$container['HashingUtilities']         = function($container) { return new \App\Helpers\HashingUtilities($container->get('settings')['app']['hash']); };
$container['randomlib']                = function($container) { return (new RandomLib\Factory)->getMediumStrengthGenerator(); };
$container['auth']                     = function($container) { return new \App\Auth\Auth($container->get('settings')['auth'], $container->HashingUtilities, $container->randomlib); };
$container['AuthenticationController'] = function($container) { return new \App\Controllers\AuthenticationController($container); };
$container['AdministrationController'] = function($container) { return new \App\Controllers\Auth\AdministrationController($container); };
$container['WikiController']           = function($container) { return new \App\Controllers\WikiController($container); };
$container['WikiPageController']       = function($container) { return new \App\Controllers\WikiPageController($container); };
$container['flash']                    = function($container) { return new \Slim\Flash\Messages; };


$container['csrf'] = function($container)
{
	$guard = new \Slim\Csrf\Guard();
	// $guard = new \Slim\Csrf\Guard(new \Slim\Psr7\Factory\ResponseFactory());

	// One CSRF token per session.
	// TODO: Ensure this is invalidated on login.
	$guard->setPersistentTokenMode(true);

	$guard->setFailureCallable(function ($request, $response, $next) {
		return $response->withJson([
			'error' => 'Failed CSRF check'
		], 400);
	});

	return $guard;
};


$container['view'] = function($container)
{
	$view = new \Slim\Views\Twig( BASE_PATH . '/../frontend/webpages', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

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


	$container['mailerView'] = function($container)
	{
		$mailerView = new \Slim\Views\Twig( BASE_PATH . '/../frontend/email', [
			'cache' => false,
		]);

		$mailerView->addExtension(new \Slim\Views\TwigExtension(
			$container->router,
			$container->request->getUri()
		));

		return $mailerView;
	};

	return new \App\Mail\Mailer($mailer, $container->mailerView);
};

/* == Middleware == */

$logger->addInfo('Adding middleware.');

$app->add(new \App\Middleware\RememberMeMiddleware($container));
$app->add(new \App\Middleware\CsrfMiddleware($container));
$app->add($container->csrf);
$app->add(new \App\Middleware\LogRequestMiddleware($container));


/* == Routes == */

$logger->addInfo('Adding the routes.');

require BASE_PATH . '/app/routes.php';

/* == Set up Globals == */

use App\Globals\FrontEndParametersFacade;

FrontEndParametersFacade::createNewFrontEndParametersInstance();
FrontEndParametersFacade::setIsAuthenticated($container->auth->isAuthenticated());
FrontEndParametersFacade::setUserData($container->auth->getUserSafely());
FrontEndParametersFacade::setBaseUrl($container->get('settings')['app']['url']);
FrontEndParametersFacade::setFlash($container->flash);

/* == Miscellaneous == */

$logger->addInfo('Completing all other (miscellaneous) bootstrapping.');

$logger->addInfo('Finished running bootstrap/app.php');
$logger->addInfo('--');

$setupFinished = true;  /* For logging purposes. */

return $app;
