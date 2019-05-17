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
 * 		return (new App\Controllers\HomeController($app))->index($request, $response, $args);
 * 	})->setName('home');
 */

global $app;
global $container;

$app->group('', function() use ($app)
{
	$app->get('/Sign_Up', 'AuthenticationController:getSignup')->setName('auth.signup');
	$app->post('/Sign_Up', 'AuthenticationController:postSignup');

	$app->get('/Sign_In', 'AuthenticationController:getSignIn')->setName('auth.signin');
	$app->post('/Sign_In', 'AuthenticationController:postSignIn');

	$app->get('/Password_Recovery', 'PasswordController:getPasswordRecovery')->setName('auth.password.recovery');
	$app->post('/Password_Recovery', 'PasswordController:postPasswordRecovery');

	$app->get('/Reset_Password', 'PasswordController:getResetPassword')->setName('auth.password.reset');
	$app->post('/Reset_Password', 'PasswordController:postResetPassword');
})->add(new GuestMiddleware($container));

$app->group('', function() use ($app)
{
	$app->get('/Sign_Out', 'AuthenticationController:getSignOut')->setName('auth.signout');

	$app->get('/Change_Password', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$app->post('/Change_Password', 'PasswordController:postChangePassword');

	/* $app->get('/Add_Wiki_Page', 'WikiPageController:getAddWebpage'); */
	$app->post('/Add_Wiki_Page', 'WikiPageController:postAddWebpage');

	/* $app->get('/Edit_Wiki_Page', 'WikiPageController:getEditWebpage'); */
	$app->post('/Edit_Wiki_Page', 'WikiPageController:postEditWebpage');

	/* $app->get('/Delete_Wiki_Page', 'WikiPageController:getDeleteWebpage'); */
	$app->post('/Delete_Wiki_Page', 'WikiPageController:postDeleteWebpage');

})->add(new AuthenticatedMiddleware($container));

$app->group('', function() use ($app)
{
	$app->get('/Administration/', 'AdministrationController:index')->setName('admin.home');
})->add(new AdministratorMiddleware($container));

$app->get('/Activate_Account', 'ActivationController:attemptActivation')->setName('activate');

/* Wiki routes */
$app->get('/', 'WikiController:serveWikiApp')->setName('home');
$app->get('/w/{pageName}', 'WikiController:serveWikiContentGetRequest');
   /* ---- */

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
