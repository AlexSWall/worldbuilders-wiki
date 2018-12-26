<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Controllers\WikiController;

$app->get('/', 'HomeController:index')->setName('home'); /* Name of controller followed by the method. */

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

$app->get('/{name}', function($request, $response, $args) {
    //return $response->write("Going to search database, when added.");
    $webpage = $this->WikiController->getWebpage($args['name']);
    if (!is_null($webpage))
    {
    	return $this->view->render($response, 'wikipage.twig', [
    		'webpage' => $webpage
    	]);
	}
	else
	{
		throw new \Slim\Exception\NotFoundException($request, $response);
	}
});

$app->getContainer()['notFoundHandler'] = function($container)
{
	return function($request, $response) use ($container)
	{
		$response = new \Slim\Http\Response(404);
		$requestPath = $request->getUri()->getPath();
		return $response->write("Page not found: ". $requestPath);
	};
};

