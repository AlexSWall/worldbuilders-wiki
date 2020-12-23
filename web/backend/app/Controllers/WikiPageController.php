<?php

namespace App\Controllers;

use App\Helpers\FrontEndDataUtilities;
use App\Helpers\ResponseUtilities;
use App\Models\WikiPage;
use App\WikitextConversion\WikitextParser;

class WikiPageController extends Controller
{
	static $logger;

	public static function getWikiPageDataResponse($response, $path, $viewingPermissions)
	{
		self::$logger->addInfo('Getting WikiPage data response');

		$returnCode = 200;

		self::$logger->addInfo('Attempting to retrieve WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		if ($wikiPage === null)
		{
			self::$logger->addInfo('Page \'' . $path . '\' not found.');

			$wikiPage = WikiPage::retrieveWikiPageByUrlPath('page-not-found');

			if ($wikiPage === null)
			{
				// :(
				self::$logger->addInfo('Failed to find \'Page Not Found\' page...');
				return $response->withStatus(500);
			}

			$returnCode = 404;
		}
		else
			self::$logger->addInfo('WikiPage found');

		self::$logger->addInfo('Constructing endpoint data array');

		$data = FrontEndDataUtilities::constructEndpointDataArray(
			$wikiPage->getUrlPath(),
			$wikiPage->getTitle(),
			$wikiPage->getHtmlForPermissionsExpression($viewingPermissions)
		);

		self::$logger->addInfo('Returning JSON');

		return $response->withJSON([
			'wikiPage' => $data
		], $returnCode, JSON_UNESCAPED_UNICODE);
	}

	public static function getWikitextResponse($response, $path)
	{
		self::$logger->addInfo('Attempting to get WikiPage\'s wikitext');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		if ($path === null)
			$errorResponse(400, 'WikiPage path not supplied.');

		self::$logger->addInfo('Checking whether WikiPage exists...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		if ($wikiPage === null)
			errorResponse(404, 'WikiPage not found.');

		self::$logger->addInfo('WikiPage exists, returning wikitext...');

		return $response->withJSON([
			'wikitext' => $wikiPage->getWikiText()
		], 200, \JSON_UNESCAPED_UNICODE);
	}

	public static function createWikiPage($response, $path, $title)
	{
		self::$logger->addInfo('Attempting to create WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->addInfo('Checking whether WikiPage exists...');

		if ( WikiPage::retrieveWikiPageByUrlPath($path) !== null )
			return $errorResponse(403, 'Page already exists.');

		self::$logger->addInfo('WikiPage doesn\'t exist, creating WikiPage...');

		$wikiPage = WikiPage::createWikiPage($path, $title);

		if ( $wikiPage === null )
			return $errorResponse(500, 'Failed to insert into database.');

		self::$logger->addInfo('Created WikiPage, returning 201...');
		return $response->withStatus(201);
	}

	public static function modifyWikiPage($response, $path, $title, $wikitext)
	{
		self::$logger->addInfo('Attempting to modify WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->addInfo('Retrieving WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		self::$logger->addInfo('Ensuring it exists...');

		if ( $wikiPage === null )
			return $errorResponse(404, 'Page not found.');

		self::$logger->addInfo('Ensuring the wikitext parses...');

		if ( !WikitextParser::checkParse($wikitext) )
			return $errorResponse(400, 'Failed to parse wikitext.');

		self::$logger->addInfo('Updating the WikiPage');

		$wikiPage->updateWikiPage($title, $wikitext);

		self::$logger->addInfo('Returning with status 204.');
		return $response->withStatus(204);
	}

	public static function deleteWikiPage($response, $path)
	{
		self::$logger->addInfo('Attempting to delete WikiPage');

		// Convenience wrapper for error response
		$errorResponse = function($errorCode, $error) use ($response)
		{
			return ResponseUtilities::respondWithError($response, $errorCode, $error);
		};

		self::$logger->addInfo('Retrieving WikiPage...');

		$wikiPage = WikiPage::retrieveWikiPageByUrlPath($path);

		self::$logger->addInfo('Ensuring it exists...');

		if ( $wikiPage === null )
			return $errorResponse(404, 'Page not found.');

		self::$logger->addInfo('Deleting the WikiPage');

		$wikiPage->delete();

		self::$logger->addInfo('Returning with status 204.');
		return $response->withStatus(204);
	}
}
