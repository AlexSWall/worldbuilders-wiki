<?php

use App\Middleware\GuestMiddleware;
use App\Middleware\AuthenticatedMiddleware;
use App\Middleware\AdministratorMiddleware;


/* Note that 
 *
 * 	$app->get('/', 'HomeController:index')->setName('home');
 *
 * is equivalent to
 *
 * 	$app->get('/', function ($request, $response, $args) {
 * 		return (new App\Controllers\HomeController($this))->index($request, $response, $args);
 * 	})->setName('home');
 */

$app->group('', function()
{
	$this->get('/Sign_Up', 'AuthController:getSignup')->setName('auth.signup');
	$this->post('/Sign_Up', 'AuthController:postSignup');

	$this->get('/Sign_In', 'AuthController:getSignIn')->setName('auth.signin');
	$this->post('/Sign_In', 'AuthController:postSignIn');

	$this->get('/Password_Recovery', 'PasswordController:getPasswordRecovery')->setName('auth.password.recovery');
	$this->post('/Password_Recovery', 'PasswordController:postPasswordRecovery');

	$this->get('/Reset_Password', 'PasswordController:getResetPassword')->setName('auth.password.reset');
	$this->post('/Reset_Password', 'PasswordController:postResetPassword');
})->add(new GuestMiddleware($container));

$app->group('', function()
{
	$this->get('/Sign_Out', 'AuthController:getSignOut')->setName('auth.signout');

	$this->get('/Change_Password', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/Change_Password', 'PasswordController:postChangePassword');
})->add(new AuthenticatedMiddleware($container));

$app->group('', function()
{
	$this->get('/admin/home', 'AdminController:index')->setName('admin.home');
})->add(new AdministratorMiddleware($container));

$app->get('/Activate_Account', 'ActivationController:attemptActivation')->setName('activate');

$app->get('/', 'WikiController:serveWikiApp')->setName('home');
$app->get('/w/{pageName}', 'WikiController:serveWikiContent');


$app->get('/{pageName}', function ($request, $response, $args) {
	return $response->withStatus(302)->withHeader('Location', '/#' . $args['pageName']);
});

$app->getContainer()['notFoundHandler'] = function($container)
{
	return function($request, $response) use ($container)
	{
		return (new App\Controllers\NotFoundController($container))->dealWithRequest($request, $response);
	};
};
