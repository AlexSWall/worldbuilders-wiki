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
	public function serveWikiContentJSONResponse($request, $response, $args)
	{
		$pageName = $args['pageName'];
		$webpage = Webpage::retrieveWebpageByName($pageName);

		if ( is_null($webpage) )
			$webpage = Webpage::retrieveWebpageByName('Page_Not_Found');

		$webpage->renderWebpageTemplateToHTML();

		$data = [
			'webpageName' => $webpage->getWebpageName(),
			'webpageTitle' => $webpage->getWebpageTitle(),
			'webpageHTML' => $webpage->getWebpageHTML()
		];

		$response = $response->withJSON($data, 200, JSON_UNESCAPED_UNICODE);

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
