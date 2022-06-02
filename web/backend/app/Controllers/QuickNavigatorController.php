<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\QuickNavigationSet;
use App\Globals\SessionFacade;
use App\Helpers\ResponseUtilities;
use App\Validation\QuickNavigationSetValidation;
use App\Validation\Validator;

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class QuickNavigatorController extends Controller
{
	public static \App\Logging\Logger $logger;

	public function serveQuickNavigatorGetRequest( Request $_request, Response $response ): Response
	{
		self::$logger->info( 'Handling Quick Navigator GET request.' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		self::$logger->info( 'Obtaining user ID.' );
		$userId = SessionFacade::getUserId();
		if ( ! $userId )
		{
			return $errorResponse( 500, 'Expected authenticated user but could not obtain user ID.' );
		}

		self::$logger->info( 'Obtained user ID, retrieving Quick Navigation Set model object.' );
		$quickNavigationSet = QuickNavigationSet::retrieveQuickNavigationSetByUserId( $userId );

		if ( ! $quickNavigationSet )
		{
			return $errorResponse( 500, 'Expected to find a Quick Navigation Set in the database for the user with user ID $userId, but none found.' );
		}

		self::$logger->info( 'Obtained Quick Navigation Set model object, getting data.' );

		$jsonDataText = $quickNavigationSet->getJson();

		$jsonData = json_decode($jsonDataText);

		if ( ! $jsonData )
		{
			return $errorResponse( 500, 'Failed to retrieve and decode Quick Navigator JSON from database.' );
		}

		self::$logger->info( 'Successfully retrieved the Quick Navigation Set data; returning JSON.' );

		return $response->withJSON( [
			'json_data' => $jsonData
		], 200, JSON_UNESCAPED_UNICODE );
	}

	public function serveQuickNavigatorPostRequest( Request $request, Response $response )
	{
		self::$logger->info( 'Handling Quick Navigator POST request.' );

		$actionStructures = [
			'set' => [
				[ $this, 'setQuickNavigationSetData' ],
				[
					'json_data' => \App\Utilities\APIUtilities::$isNonEmptyStringValidator
				]
			]
		];

		$entryFunc = \App\Utilities\APIUtilities::createPostAPIEntryPoint(
			'Quick Navigator',
			$actionStructures
		);

		return $entryFunc( $request, $response );
	}

	public function setQuickNavigationSetData( Response $response, string $jsonData ): Response
	{
		self::$logger->info( 'Attempting to set Quick Navigator data.' );

		// Convenience wrapper for error response
		$errorResponse = function ( $errorCode, $error, $extraErrorData ) use ( $response )
		{
			return ResponseUtilities::getApiErrorResponse( $response, $errorCode, $error, $extraErrorData );
		};

		$errors = Validator::validate([
			'json_data' => [ QuickNavigationSetValidation::quickNavigationSetDataRules(), $jsonData ],
		]);

		if ( ! empty( $errors ) )
		{
			return $errorResponse( 400, 'Validation failure', ['validation_errors' => $errors] );
		}

		self::$logger->info( 'Validation passed, obtaining user ID.' );
		$userId = SessionFacade::getUserId();
		if ( ! $userId )
		{
			return $errorResponse( 500, 'Expected authenticated user but could not obtain user ID.' );
		}

		self::$logger->info( 'Obtained user ID, retrieving Quick Navigation Set model object.' );
		$quickNavigationSet = QuickNavigationSet::retrieveQuickNavigationSetByUserId( $userId );

		if ( ! $quickNavigationSet )
		{
			return $errorResponse( 500, 'Expected to find a Quick Navigation Set in the database for the user with user ID $userId, but none found.' );
		}

		self::$logger->info( 'Obtained Quick Navigation Set model object, setting data.' );

		$quickNavigationSet->setJson( $jsonData );

		self::$logger->info( 'Successfully set Quick Navigation Set data.' );
	}
}
