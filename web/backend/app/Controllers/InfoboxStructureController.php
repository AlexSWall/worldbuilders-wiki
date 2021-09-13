<?php declare( strict_types = 1 );

namespace App\Controllers;

use App\Helpers\ResponseUtilities;
use App\Infoboxes\InfoboxParser;
use App\Models\Infobox;

use Slim\Http\Response;

class InfoboxController extends Controller
{
	static \App\Logging\Logger $logger;

	public static function getInfoboxStructureNamesResponse( Response $response ): Response
	{
		self::$logger->info('Getting infobox structure names');

		$infoboxNames = \App\Models\SpecialisedQueries\InfoboxQueries::getInfoboxNames();

		return $response->withJSON([
			'infobox_names' => $infoboxNames
		], 200, \JSON_UNESCAPED_UNICODE);
	}

	public static function getInfoboxStructureResponse( Response $response, string $infoboxName ): Response
	{
		self::$logger->info('Getting infobox structure for infobox \'' . $infoboxName . '\'');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		$infobox = Infobox::retrieveInfoboxByName( $infoboxName );

		if ( $infobox === null )
		{
			self::$logger->info('Infobox \'' . $infoboxName . '\' not found.');

			// Early 404 return
			return $errorResponse( 404, 'Infobox doesn\'t exist' );
		}

		self::$logger->info('Found infobox \'' . $infoboxName . '\'');

		$infoboxStructureText = $infobox->getRawText();

		return $response->withJSON([
			'infobox_structure_text' => $infoboxStructureText
		], 200, \JSON_UNESCAPED_UNICODE);
	}

	public static function createInfobox( Response $response, string $infoboxName ): Response
	{
		self::$logger->info('Attempting to create infobox');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Checking whether infobox exists...');

		if ( Infobox::retrieveInfoboxByName($infoboxName) !== null )
			return $errorResponse(403, 'Page already exists.');

		self::$logger->info('Infobox doesn\'t exist, creating infobox...');

		$infobox = Infobox::createInfobox( $infoboxName, '' );

		if ( $infobox === null )
			return $errorResponse(500, 'Failed to insert into database.');

		self::$logger->info('Created infobox, returning 201...');
		return $response->withStatus(201);
	}

	public static function modifyInfobox( Response $response, string $infoboxName, $structure ): Response
	{
		self::$logger->info('Attempting to modify infobox');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Retrieving infobox...');

		$infobox = Infobox::retrieveInfoboxByName($infoboxName);

		self::$logger->info('Ensuring it exists...');

		if ( $infobox === null )
			return $errorResponse(404, 'Page not found.');

		self::$logger->info('Ensuring the infobox structure parses...');

		$infoboxItems = InfoboxParser::parse( $structure );
		if ( ! $infoboxItems )
			return $errorResponse(400, 'Failed to parse infobox structure.');

		self::$logger->info('Updating the infobox structure');

		$infobox->setInfoboxItems( $infoboxItems );

		self::$logger->info('Returning with status 204.');
		return $response->withStatus(204);
	}

	public static function deleteInfobox( Response $response, string $infoboxName ): Response
	{
		self::$logger->info('Attempting to delete infobox');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Retrieving infobox...');

		$infobox = Infobox::retrieveInfoboxByName( $infoboxName );

		self::$logger->info('Ensuring it exists...');

		if ( $infobox === null )
			return $errorResponse(404, 'Infobox not found.');

		self::$logger->info('Deleting the infobox');

		$infobox->delete();

		self::$logger->info('Returning with status 204.');
		return $response->withStatus(204);
	}
}
