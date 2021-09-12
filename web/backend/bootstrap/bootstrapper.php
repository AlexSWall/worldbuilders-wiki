<?php declare( strict_types = 1 );

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

spl_autoload_register( function( string $class_name ) use ($logger, $logger_names, &$setupFinished): void
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';

	$class_path = str_replace('/App/', '/app/', $class_path);

	if ( file_exists($class_path) )
	{
		if ( $setupFinished )
			$logger->info('Including ' . $class_name);

		include $class_path; /* No need for include_once; if it had been included, we wouldn't be here. */

		/* Logging black magic: initialising the static loggers after autoloading classes. */
		foreach ( $logger_names as $logger_name => $logger_ref )
			if ( property_exists($class_name, $logger_name) )
				$class_name::$$logger_name = \App\Logging\LoggerRegistry::get($logger_ref);
	}
} );

$logger->info('---------------');
$logger->info('Setup starting.');

session_start();

/* == Create Container Builder == */

$containerBuilder = new DI\ContainerBuilder();

// Add configuration
$containerBuilder->addDefinitions([
	'settings' => $config,
]);

$capsule = new \Illuminate\Database\Capsule\Manager; /* Use database component outside of Laravel. */
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

use Psr\Container\ContainerInterface;

// Add dependencies/container items
$containerBuilder->addDefinitions(
[
	'loggers' => $loggers,

	'HashingUtilities' => function( ContainerInterface $container ): \App\Helpers\HashingUtilities
		{
			return new \App\Helpers\HashingUtilities($container->get('settings')['app']['hash']);
		},

	'randomlib' => function(): \RandomLib\Generator
		{
			return (new RandomLib\Factory)->getMediumStrengthGenerator();
		},

	'auth' => function(ContainerInterface $container): \App\Auth\Auth
		{
			return new \App\Auth\Auth($container->get('settings')['auth'], $container->get('HashingUtilities'), $container->get('randomlib'));
		},

	'AuthenticationController' => function( ContainerInterface $container ): \App\Controllers\AuthenticationController
		{
			return new \App\Controllers\AuthenticationController($container);
		},

	'AdministrationController' => function( ContainerInterface $container ): \App\Controllers\AdministrationController
		{
			return new \App\Controllers\AdministrationController($container);
		},

	'WikiController' => function( ContainerInterface $container ): \App\Controllers\WikiController
		{
			return new \App\Controllers\WikiController($container);
		},

	'WikiPageController' => function( ContainerInterface $container ): \App\Controllers\WikiPageController
		{
			return new \App\Controllers\WikiPageController($container);
		},

	'view' => function(): \Slim\Views\Twig
		{
			$view = \Slim\Views\Twig::create( BASE_PATH . '/../frontend/webpages', [
				'cache' => false,
			]);

			/* $view->addExtension(new \Slim\Views\TwigExtension( */
			/* 	$container->router, */
			/* 	$container->request->getUri() */
			/* )); */

			return $view;
		},

	'db' => function() use ($capsule): \Illuminate\Database\Capsule\Manager
		{
			return $capsule;
		},

	'mailer' => function( DI\Container $container ): \App\Mail\Mailer
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


			$container->set('mailerView', function(): \Slim\Views\Twig
			{
				$mailerView = \Slim\Views\Twig::create( BASE_PATH . '/../frontend/email', [
					'cache' => false,
				]);

				/* $mailerView->addExtension(new \Slim\Views\TwigExtension( */
				/* 	$container->router, */
				/* 	$container->request->getUri() */
				/* )); */

				return $mailerView;
			});

			return new \App\Mail\Mailer($mailer, $container->get('mailerView'));
		}
]);


/* == Build Container == */

$container = $containerBuilder->build();

Slim\Factory\AppFactory::setContainer($container);
$app = Slim\Factory\AppFactory::create();

$app->setBasePath('');


/* == Middleware == */

$logger->info('Adding middleware.');

// Log user in if they have a Remember Me cookie
{
	$app->add(new \App\Middleware\RememberMeMiddleware($container));
}

// CSRF guard and globals middleware
{
	$csrfGuard = new \Slim\Csrf\Guard($app->getResponseFactory());

	// One CSRF token per session.
	// TODO: Ensure this is invalidated on login.
	$csrfGuard->setPersistentTokenMode(true);

	$csrfGuard->setFailureHandler(function ($request, $handler): \Psr\Http\Message\ResponseInterface
	{
		$response = new \Slim\Psr7\Response;

		$response->withStatus(400);

		$body = json_encode([
			'error' => 'Failed CSRF check'
		]);
		$response->getBody()->write($body);

		return $response;
	});

	// Set CSRF token global variables.
	$app->add(new \App\Middleware\CsrfMiddleware($container, $csrfGuard));

	// Check previous CSRF Token
	$app->add($csrfGuard);
}

// Add middleware to log all requests and responses
{
	$app->add(new \App\Middleware\LogRequestMiddleware($container));
}

// Error-handling middleware
{
	/**
	 * Add Error Middleware
	 *
	 * @param bool                  $displayErrorDetails -> Should be set to false in production
	 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
	 * @param bool                  $logErrorDetails -> Display error details in error log
	 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
	 *
	 * Note: This middleware should be added last. It will not handle any exceptions/errors
	 * for middleware added after it.
	 */
	$errorMiddleware = $app->addErrorMiddleware(true, true, true, \App\Logging\LoggerRegistry::get('general'));
}

/* == Routes == */

$logger->info('Adding the routes.');

require BASE_PATH . '/app/routes.php';

/* == Set up Globals == */

use App\Globals\FrontEndParametersFacade;

FrontEndParametersFacade::createNewFrontEndParametersInstance();
FrontEndParametersFacade::setIsAuthenticated($container->get('auth')->isAuthenticated());
FrontEndParametersFacade::setHashingUtilities($container->get('HashingUtilities'));
{
	$maybeUser = $container->get('auth')->getUserSafely();
	if ( $maybeUser )
		FrontEndParametersFacade::setUserData($maybeUser);
}
FrontEndParametersFacade::setBaseUrl($container->get('settings')['app']['url']);

/* == Miscellaneous == */

$logger->info('Completing all other (miscellaneous) bootstrapping.');

$logger->info('Finished running bootstrap/app.php');
$logger->info('--');

$setupFinished = true;  /* For logging purposes. */

return $app;
