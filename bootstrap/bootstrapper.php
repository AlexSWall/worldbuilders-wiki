<?php

$setupFinished = false;  /* For logging purposes. */

/* == Load dependencies == */

assert(isset($config));

if ( ! defined('BASE_PATH') )
	define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php'; /* Load dependencies with composer */

require BASE_PATH . '/App/Logging/LoggerRegistry.php';
require BASE_PATH . '/App/Logging/Logger.php';

/* == Set up logging == */

$logger_names = ['logger' => \App\Logging\LoggerRegistry::GENERAL_LOGGER];

foreach ($config['loggers'] as $logger_ref => $logger_config)
{
	\App\Logging\LoggerRegistry::addLoggerFromConfig($logger_ref, $logger_config);
	$logger_names[$logger_config['logger_name']] = $logger_ref;
}

$logger = \App\Logging\LoggerRegistry::get(\App\Logging\LoggerRegistry::SETUP_LOGGER);

spl_autoload_register( function( $class_name ) use ($logger, $logger_names, &$setupFinished)
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';
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

$container['HashingUtilities']         = function($container) { return new \App\Helpers\HashingUtilities($container->get('settings')['app']['hash']); };
$container['randomlib']                = function($container) { return (new RandomLib\Factory)->getMediumStrengthGenerator(); };
$container['auth']                     = function($container) { return new \App\Auth\Auth($container->get('settings')['auth'], $container->HashingUtilities, $container->randomlib); };
$container['validator']                = function($container) { return new \App\Validation\Validator; };
$container['HomeController']           = function($container) { return new \App\Controllers\HomeController($container); };
$container['AuthenticationController'] = function($container) { return new \App\Controllers\Auth\AuthenticationController($container); };
$container['AdministrationController'] = function($container) { return new \App\Controllers\Auth\AdministrationController($container); };
$container['PasswordController']       = function($container) { return new \App\Controllers\Auth\PasswordController($container); };
$container['ActivationController']     = function($container) { return new \App\Controllers\Auth\ActivationController($container); };
$container['WikiController']           = function($container) { return new \App\Controllers\WikiController($container); };
$container['WikiPageController']       = function($container) { return new \App\Controllers\WikiPageController($container); };
$container['csrf']                     = function($container) { return new \Slim\Csrf\Guard; };
$container['flash']                    = function($container) { return new \Slim\Flash\Messages; };


$container['view'] = function($container)
{
	$view = new \Slim\Views\Twig( BASE_PATH . '/View/Webpages', [
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
		$mailerView = new \Slim\Views\Twig( BASE_PATH . '/View/Email', [
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

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\RememberMeMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);
$app->add(new \App\Middleware\LogRequestMiddleware($container));


/* == Routes == */

$logger->addInfo('Adding the routes.');

require BASE_PATH . '/App/routes.php';

/* == Set up Globals == */

\App\Globals\FrontEndParametersFacade::createNewFrontEndParametersInstance();
\App\Globals\FrontEndParametersFacade::setIsAuthenticated($container->auth->isAuthenticated());
\App\Globals\FrontEndParametersFacade::setUserData($container->auth->getUserSafely());
\App\Globals\FrontEndParametersFacade::setBaseUrl($container->get('settings')['app']['url']);
\App\Globals\FrontEndParametersFacade::setFlash($container->flash);

/* == Miscellaneous == */

$logger->addInfo('Completing all other (miscellaneous) bootstrapping.');

Respect\Validation\Validator::with('App\\Validation\\Rules\\');

$logger->addInfo('Finished running bootstrap/app.php');
$logger->addInfo('--');

$setupFinished = true;  /* For logging purposes. */

return $app;
