<?php

declare(strict_types=1);

/* use App\Middleware\GuestMiddleware; */
use App\Middleware\AuthenticatedMiddleware;
use App\Middleware\AdministratorMiddleware;
use App\Helpers\ResponseUtilities;

use Slim\Http\ServerRequest as Request;
use Slim\Http\Response;

assert( isset( $container ) );

/* Note:
 *    $app->get('/', 'Foo:bar');
 * gives
 *    $app->get('/', function ($request, $response, $args) {
 *       return $this->Foo->bar($request, $response, $args);
 *    });
 */


/* ------ */
/*  Wiki  */
/* ------ */

/* Home /#<PageName> */
$app->get( '/', 'WikiController:serveWikiApp' )->setName( 'home' );

/* 302: /<PageName> -> /#<PageName> */
$app->get( '/{page}', function ( Request $_request, Response $response, array $args ) {
	$page = $args['page'];
	return ResponseUtilities::respondWithRedirect( $response, '/#' . $page );
} );


/* -------- */
/* Wiki API */
/* -------- */

/* Get Wiki Page Contents /w/<PageName> */
$app->get( '/w/{wikipage:.*}', 'WikiController:serveWikiContentGetRequest' );

/* Logged-In Wiki APIs */
$app->group( '', function () use ( $app )
{
	/* Wiki Quick Navigator */
	$app->get( '/u/quick-navigator', 'QuickNavigatorController:serveQuickNavigatorGetRequest' );
	$app->post( '/u/quick-navigator', 'QuickNavigatorController:serveQuickNavigatorPostRequest' );
} )->add( new AuthenticatedMiddleware( $container, true ) );

/* Admin Wiki APIs */
$app->group( '', function () use ( $app )
{
	/* Wiki Page APIs */
	$app->get( '/a/wiki', 'WikiController:serveEditWikiPageGetRequest' );
	$app->post( '/a/wiki', 'WikiController:serveModifyWikiContentPostRequest' );

	/* Infobox Structure APIs */
	$app->get( '/a/infobox', 'WikiController:serveInfoboxStructureGetRequest' );
	$app->post( '/a/infobox', 'WikiController:serveModifyInfoboxStructurePostRequest' );
} )->add( new AdministratorMiddleware( $container, true ) );


/* ------------------ */
/* Account Management */
/* ------------------ */

// -- GET Requests --
$app->get( '/auth/activate-account', 'AuthenticationController:serveActivationAttempt' )
	 ->setName( 'activate-account' );

$app->get( '/auth/reset-password', 'AuthenticationController:serveResetPasswordGetRequest' )
    ->setName( 'reset-password' );

// -- POST Requests --
$app->post( '/auth/', 'AuthenticationController:servePostRequest' );


/* -------------- */
/* Administration */
/* -------------- */

$app->group( '', function () use ( $app )
{
	$app->get( '/admin/', 'AdministrationController:index' );
} )->add( new AdministratorMiddleware( $container, false ) );
