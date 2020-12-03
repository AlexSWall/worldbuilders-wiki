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

/* ------------------ */
/* Account Management */
/* ------------------ */

/* Guest */
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

/* Account Activation /Activate_Acount */
$app->get('/Activate_Account', 'ActivationController:attemptActivation')->setName('activate');

/* Authenticated Routes */
$app->group('', function() use ($app)
{
	$app->get('/Sign_Out', 'AuthenticationController:getSignOut')->setName('auth.signout');

	$app->get('/Change_Password', 'PasswordController:getChangePassword')->setName('auth.password.change');
	$app->post('/Change_Password', 'PasswordController:postChangePassword');
})->add(new AuthenticatedMiddleware($container));


/* -------------- */
/* Administration */
/* -------------- */

$app->group('', function() use ($app)
{
	$app->get('/admin/', 'AdministrationController:index')->setName('admin.home');
})->add(new AdministratorMiddleware($container, false));


/* ------ */
/*  Wiki  */
/* ------ */

/* Home /#<PageName> */
$app->get('/', 'WikiController:serveWikiApp')->setName('home');

/* 302: /<PageName> -> /#<PageName> */
$app->get('/{pageName}', function ($request, $response, $args) {
	return $response->withStatus(302)->withHeader('Location', '/#' . $args['pageName']);
});


/* -------- */
/* Wiki API */
/* -------- */

/* Get Wiki Page Contents /w/<PageName> */
$app->get('/w/{pageName}', 'WikiController:serveWikiContentGetRequest');

/* Admin Wiki Page API */
$app->group('', function() use ($app)
{
	$app->get('/a/wiki', 'WikiController:getWikitext');
	$app->post('/a/wiki', 'WikiController:modifyWikiContentPostRequest');
})->add(new AdministratorMiddleware($container, true));


/* ----- */
/* Other */
/* ----- */

/* Page Not Found */
$app->getContainer()['notFoundHandler'] = function($container)
{
	return function($request, $response) use ($container)
	{
		return (new App\Controllers\NotFoundController($container))->dealWithRequest($request, $response);
	};
};
