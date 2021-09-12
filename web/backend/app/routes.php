<?php declare( strict_types = 1 );

/* use App\Middleware\GuestMiddleware; */
/* use App\Middleware\AuthenticatedMiddleware; */
use App\Middleware\AdministratorMiddleware;

use Psr\Container\ContainerInterface;

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response;

assert(isset($container));

/* Note:
 * 	$app->get('/', 'Foo:bar');
 * gives
 * 	$app->get('/', function ($request, $response, $args) {
 * 		return $this->Foo->bar($request, $response, $args);
 * 	});
 */

/* ------ */
/*  Wiki  */
/* ------ */

/* Home /#<PageName> */
$app->get('/', 'WikiController:serveWikiApp')->setName('home');

/* 302: /<PageName> -> /#<PageName> */
$app->get('/{wikipage}', function (Request $request, Response $response, array $args) {
	return $response->withStatus(302)->withHeader('Location', '/#' . $args['wikipage']);
});

/* -------- */
/* Wiki API */
/* -------- */

/* Get Wiki Page Contents /w/<PageName> */
$app->get('/w/{wikipage:.*}', 'WikiController:serveWikiContentGetRequest');

/* Admin Wiki Page API */
$app->group('', function() use ($app)
{
	$app->get('/a/wiki', 'WikiController:serveWikitext');
	$app->post('/a/wiki', 'WikiController:serveModifyWikiContentPostRequest');
})->add(new AdministratorMiddleware($container, true));

/* ------------------ */
/* Account Management */
/* ------------------ */

// -- GET Requests --
$app->get('/auth/activate-account', 'AuthenticationController:serveActivationAttempt')
	 ->setName('activate-account');

$app->get('/auth/reset-password', 'AuthenticationController:serveResetPasswordGetRequest')
    ->setName('reset-password');

// -- POST Requests --
$app->post('/auth/', 'AuthenticationController:servePostRequest');

/* -------------- */
/* Administration */
/* -------------- */

$app->group('', function() use ($app)
{
	$app->get('/admin/', 'AdministrationController:index');
})->add(new AdministratorMiddleware($container, false));
