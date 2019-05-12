<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	/* Request of form <URL>/#{pageName} */
	public function serveWikiApp($request, $response)
	{
		$params = self::getFrontendParametersArray();
		return $this->view->render($response, 'wiki/index.twig', $params);
	}

	/* Request of <URL>/w/{pageName} */
	public function serveWikiContent($request, $response, $args)
	{
		$pageName = $args['pageName'];
		$webpage = Webpage::retrieveWebpageByName($pageName);

		$body = $response->getBody();
		if ( is_null($webpage) )
		{
			$webpage = Webpage::retrieveWebpageByName('Page_Not_Found');
		}

		$body->write($webpage->getWebpageHTML());

		return $response;
	}

	/* == Helpers == */

	private static function getFrontendParametersArray()
	{
		return [
			'auth' => $GLOBALS['auth'],
			'flash' => $GLOBALS['flash'],
			'baseUrl' => $GLOBALS['baseUrl']
		];
	}
}
