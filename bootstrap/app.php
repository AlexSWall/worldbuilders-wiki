<?php

spl_autoload_register(function( $class_name )
{
	$class_path = BASE_PATH . '/' . str_replace('\\', '/', $class_name) . '.php';
	if ( file_exists($class_path) )
	{
	    include $class_path; /* No need for include_once; if it had been included, we wouldn't be here. */
	}
});

session_start();

require BASE_PATH . '/vendor/autoload.php'; /* Load dependencies with composer */

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
		'addContentLengthHeader' => false,
		'db' => [
			'driver' => 'mysql',
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'TheIncredibles',
			'database' => 'website',
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => ''
		]
	]
]);

$container = $app->getContainer();

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(BASE_PATH . '/logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container->logger->addInfo('Logging added.');
$container->logger->addInfo('Populating the container.');

$capsule = new \Illuminate\Database\Capsule\Manager; /* Use database component outside of Laravel. */
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule)
{
	return $capsule;
};

$container['auth'] = function($container)
{
	return new \App\Auth\Auth;
};

$container['view'] = function($container)
{
	$view = new \Slim\Views\Twig( BASE_PATH . '/resources/views', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->getEnvironment()->addGlobal('auth', [
		'check' => $container->auth->check(),
		'user' => $container->auth->user()
	]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
};

$container['validator'] = function($container)
{
	return new App\Validation\Validator;
};

$container['HomeController'] = function($container)
{
	return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container)
{
	return new \App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function($container)
{
	return new \App\Controllers\Auth\PasswordController($container);
};

$container['WikiController'] = function($container)
{
	return new \App\Controllers\WikiController($container);
};

$container['csrf'] = function($container)
{
	return new \Slim\Csrf\Guard;
};

$container['flash'] = function($container)
{
	return new \Slim\Flash\Messages;
};

$container->logger->addInfo('Adding middleware.');

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

use Respect\Validation\Validator as v;
v::with('App\\Validation\\Rules\\');

$container->logger->addInfo('Adding the routes.');

require BASE_PATH . '/App/routes.php';

$container->logger->addInfo('Finished running bootstrap/app.php');
