<?php

session_start();

spl_autoload_register( function( $class_name )
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';
	if ( file_exists($class_path) )
	    include $class_path; /* No need for include_once; if it had been included, we wouldn't be here. */
} );

require BASE_PATH . '/vendor/autoload.php'; /* Load dependencies with composer */

$mode = file_get_contents(BASE_PATH . '/mode.php');
$config = include (BASE_PATH . '/config/' . $mode . '.config');

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();


/* == Container Items == */

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(BASE_PATH . '/logs/app.log');
    $formatter = new \Monolog\Formatter\LineFormatter(null, null, false, true);
    $file_handler->setFormatter($formatter);
    $logger->pushHandler($file_handler);
    return $logger;
};

$container->logger->addInfo('Logging added.');
$container->logger->addInfo('Populating the container.');

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
	$view = new \Slim\Views\Twig( BASE_PATH . '/resources/views', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$check = $container->auth->check();
	$user = null;
	$isAdmin = null;

	if ( $check )
	{
		$user = $container->auth->user();
		$isAdmin = $user->isAdmin();
	}

	$view->getEnvironment()->addGlobal('auth', [
		'check' => $check,
		'user' => $user, /* This runs automatically, even if left uncalled, so we must get the user 'safely'. */
		'isAdmin' => $isAdmin
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

$container->logger->addInfo('Adding middleware.');

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\RememberMeMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);


/* == Routes == */

$container->logger->addInfo('Adding the routes.');

require BASE_PATH . '/App/routes.php';

/* == Miscellaneous == */

$container->logger->addInfo('Completing all other (miscellaneous) bootstrapping.');

Respect\Validation\Validator::with('App\\Validation\\Rules\\');

$container->logger->addInfo('Finished running bootstrap/app.php');
