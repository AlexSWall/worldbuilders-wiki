<?php declare( strict_types = 1 );

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;
use App\Helpers\ResponseUtilities;
use App\Models\WikiPage;
use App\Utilities\ArrayBasedSet;
use App\WikitextConversion\WikitextParser;

use Slim\Http\Response;

class WikiPageController extends Controller
{
	static \App\Logging\Logger $logger;

	public static function getWikiPageDataResponse(Response $response, string $path, ?ArrayBasedSet $viewingPermissions): Response
	{
		self::$logger->info('Getting WikiPage data response');

		$returnCode = 200;

		self::$logger->info('Attempting to retrieve WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		if ($wikiPage === null)
		{
			self::$logger->info('Page \'' . $path . '\' not found.');

			$wikiPage = WikiPage::retrieveWikiPageByUrlPath('page-not-found');

			if ($wikiPage === null)
			{
				// :(
				self::$logger->info('Failed to find \'Page Not Found\' page...');
				return $response->withStatus(500);
			}

			$returnCode = 404;
		}
		else
			self::$logger->info('WikiPage found');

		self::$logger->info('Constructing endpoint data array');

		$data = FrontEndDataUtilities::constructEndpointDataArray(
			$wikiPage->getUrlPath(),
			$wikiPage->getTitle(),
			$wikiPage->getHtmlForPermissions($viewingPermissions)
		);

		self::$logger->info('Returning JSON');

		return $response->withJSON([
			'wikiPage' => $data
		], $returnCode, JSON_UNESCAPED_UNICODE);
	}

	public static function getWikitextResponse(Response $response, string $path): Response
	{
		self::$logger->info('Attempting to get WikiPage\'s wikitext');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		if ($path === null)
			$errorResponse(400, 'WikiPage path not supplied.');

		self::$logger->info('Checking whether WikiPage exists...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		if ($wikiPage === null)
			$errorResponse(404, 'WikiPage not found.');

		self::$logger->info('WikiPage exists, returning wikitext...');

		return $response->withJSON([
			'wikitext' => $wikiPage->getWikiText()
		], 200, \JSON_UNESCAPED_UNICODE);
	}

	public static function createWikiPage(Response $response, string $path, string $title): Response
	{
		self::$logger->info('Attempting to create WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Checking whether WikiPage exists...');

		if ( WikiPage::retrieveWikiPageByUrlPath($path) !== null )
			return $errorResponse(403, 'Page already exists.');

		self::$logger->info('WikiPage doesn\'t exist, creating WikiPage...');

		$wikiPage = WikiPage::createWikiPage($path, $title);

		if ( $wikiPage === null )
			return $errorResponse(500, 'Failed to insert into database.');

		self::$logger->info('Created WikiPage, returning 201...');
		return $response->withStatus(201);
	}

	public static function modifyWikiPage(Response $response, string $path, string $title, string $wikitext): Response
	{
		self::$logger->info('Attempting to modify WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Retrieving WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		self::$logger->info('Ensuring it exists...');

		if ( $wikiPage === null )
			return $errorResponse(404, 'Page not found.');

		self::$logger->info('Ensuring the wikitext parses...');

		if ( !WikitextParser::checkParse($wikitext) )
			return $errorResponse(400, 'Failed to parse wikitext.');

		self::$logger->info('Updating the WikiPage');

		$wikiPage->updateWikiPage($title, $wikitext);

		self::$logger->info('Returning with status 204.');
		return $response->withStatus(204);
	}

	public static function deleteWikiPage(Response $response, string $path): Response
	{
		self::$logger->info('Attempting to delete WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->info('Retrieving WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		self::$logger->info('Ensuring it exists...');

		if ( $wikiPage === null )
			return $errorResponse(404, 'Page not found.');

		self::$logger->info('Deleting the WikiPage');

		$wikiPage->delete();

		self::$logger->info('Returning with status 204.');
		return $response->withStatus(204);
	}
}
