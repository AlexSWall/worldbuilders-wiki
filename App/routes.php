<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/* Equivalent to
 * $app->get('/', 'HomeController:index')->setName('home');
 */
$app->get('/', function ($request, $response, $args) {
	return (new App\Controllers\HomeController($this))->index($request, $response, $args);
})->setName('home');

$app->group('', function()
{
	$this->get('/auth/signup', 'AuthController:getSignup')->setName('auth.signup');
	$this->post('/auth/signup', 'AuthController:postSignup');

	$this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function()
{
	$this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

	$this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/auth/password/change', 'PasswordController:postChangePassword');
})->add(new AuthMiddleware($container));

$app->get('/{name}', 'WikiController:serveWebpage');

$app->getContainer()['notFoundHandler'] = function($container)
{
	return function($request, $response) use ($container)
	{
		$response = new \Slim\Http\Response(404);
		$requestPath = $request->getUri()->getPath();
		return $response->write("Page not found: ". $requestPath);
	};
};

