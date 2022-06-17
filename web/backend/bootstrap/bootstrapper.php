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

$loggerNames = ['logger' => \App\Logging\LoggerRegistry::GENERAL_LOGGER];

foreach ($config['loggers'] as $loggerRef => $loggerConfig)
{
	\App\Logging\LoggerRegistry::addLoggerFromConfig($loggerRef, $loggerConfig);
	$loggerNames[$loggerConfig['logger_name']] = $loggerRef;
}

$loggers = [];
foreach ( $loggerNames as $loggerName => $loggerRef )
	$loggers[$loggerName] = \App\Logging\LoggerRegistry::get($loggerRef);


$logger = \App\Logging\LoggerRegistry::get(\App\Logging\LoggerRegistry::SETUP_LOGGER);

spl_autoload_register( function( string $className ) use ( $logger, $loggerNames, &$setupFinished ): void
{
	$classPath = BASE_PATH . '/' . str_replace( '\\', '/', $className ) . '.php';

	$classPath = str_replace( '/App/', '/app/', $classPath );

	if ( file_exists($classPath) )
	{
		if ( $setupFinished )
			$logger->info( 'Including ' . $className );

		include $classPath; /* No need for include_once; if it had been included, we wouldn't be here. */

		/* Logging black magic: initialising the static loggers after autoloading classes. */
		foreach ( $loggerNames as $loggerName => $loggerRef )
		{
			if ( property_exists( $className, $loggerName ) )
			{
				// TODO: May be slow; benchmark
				// Required to get around `private` properly of static loggers.
				/* $reflectionProperty = new \ReflectionProperty( $className, $loggerName ); */
				/* $reflectionProperty->setAccessible( true ); */
				/* $reflectionProperty->setValue( $className, \App\Logging\LoggerRegistry::get( $loggerRef ) ); */
				$className::$$loggerName = \App\Logging\LoggerRegistry::get($loggerRef);
			}
		}
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

	'QuickNavigatorController' => function( ContainerInterface $container ): \App\Controllers\QuickNavigatorController
		{
			return new \App\Controllers\QuickNavigatorController($container);
		},

	'WikiController' => function( ContainerInterface $container ): \App\Controllers\WikiController
		{
			return new \App\Controllers\WikiController($container);
		},

	'WikiPageController' => function( ContainerInterface $container ): \App\Controllers\WikiPageController
		{
			return new \App\Controllers\WikiPageController($container);
		},

	'db' => function() use ($capsule): \Illuminate\Database\Capsule\Manager
		{
			return $capsule;
		},

	'view' => function(): \Slim\Views\Twig
		{
			$view = \Slim\Views\Twig::create( BASE_PATH . '/../frontend/webpages', [
				'cache' => false,
			]);

			return $view;
		},

	'mailer-view' => function(): \Slim\Views\Twig
		{
			$mailerView = \Slim\Views\Twig::create( BASE_PATH . '/../frontend/email', [
				'cache' => false,
			]);

			return $mailerView;
		},

	'mailer' => function( DI\Container $container ): \App\Mail\Mailer
		{
			$mailerSettings = $container->get('settings')['mail'];

			$mailer = new PHPMailer\PHPMailer\PHPMailer( $mailerSettings['throw_exceptions'] );

			$mailer->Host = $mailerSettings['host'];
			$mailer->Port = $mailerSettings['port'];

			$mailer->isSMTP();
			$mailer->SMTPAuth = true;
			$mailer->SMTPSecure = $mailerSettings['smtp_secure'];
			$mailer->AuthType = 'XOAUTH2';

			$mailer->Username = $mailerSettings['email'];

			$mailer->setOAuth(
				new PHPMailer\PHPMailer\OAuth(
					[
						'provider' => new League\OAuth2\Client\Provider\Google(
							[
								'clientId' => $mailerSettings['oauth_client_id'],
								'clientSecret' => $mailerSettings['oauth_client_secret'],
							]),
						'clientId' => $mailerSettings['oauth_client_id'],
						'clientSecret' => $mailerSettings['oauth_client_secret'],
						'refreshToken' => $mailerSettings['oauth_refresh_token'],
						'userName' => $mailerSettings['email'],
					]
				)
			);

			$mailer->isHTML($mailerSettings['html']);
			$mailer->setFrom($mailerSettings['email'], $mailerSettings['from_name']);

			return new \App\Mail\Mailer($mailer, $container->get('mailer-view'));
		}
]);


/* == Build Container == */

$container = $containerBuilder->build();

Slim\Factory\AppFactory::setContainer($container);

$app = Slim\Factory\AppFactory::create();

$app->setBasePath('');


/* == Middleware == */

$logger->info('Adding middleware.');

// Add Twig-View Middleware
{
	$app->add(Slim\Views\TwigMiddleware::createFromContainer($app, 'view'));
	$app->add(Slim\Views\TwigMiddleware::createFromContainer($app, 'mailer-view'));
}

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

	$csrfGuard->setFailureHandler(function ($_request, $_handler): \Psr\Http\Message\ResponseInterface
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


/* == Set up Session == */

use App\Globals\SessionFacade;

SessionFacade::initializeSessionFacade( $container->get('settings')['auth'] );


/* == Set up Globals == */

use App\Globals\GlobalsFacade;

GlobalsFacade::createNewGlobalsInstance();
GlobalsFacade::setIsAuthenticated($container->get('auth')->isAuthenticated());
GlobalsFacade::setHashingUtilities($container->get('HashingUtilities'));
{
	$maybeUser = $container->get('auth')->getUser();
	if ( $maybeUser )
		GlobalsFacade::setUserData( $maybeUser );
}
GlobalsFacade::setBaseUrl($container->get('settings')['app']['url']);


/* == Miscellaneous == */

$logger->info('Completing all other (miscellaneous) bootstrapping.');

$logger->info('Finished running bootstrap/app.php');
$logger->info('--');

$setupFinished = true;  /* For logging purposes. */

return $app;
