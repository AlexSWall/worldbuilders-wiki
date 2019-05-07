<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	public function serveWebpage($request, $response, $args)
	{
		$pageName = $args['page_name'];
		$title = str_replace('_', ' ', $pageName);

		$webpage = Webpage::retrieveWebpageByName($pageName);

		if ( is_null($webpage) )
			throw new \Slim\Exception\NotFoundException($request, $response);

		$webpageHTML = $webpage->getWebpageHTML($pageName);

		$params = self::constructFrontendParametersArray($title, $webpageHTML);

		return $this->view->render($response, 'wiki/index.twig', $params);
	}

	private static function constructFrontendParametersArray($title, $webpageHTML)
	{
		return [
			'wiki' => [
				'title' => $title,
				'webpageContent' => $webpageHTML
			],
			'auth' => $GLOBALS['auth'],
			'flash' => $GLOBALS['flash'],
			'baseUrl' => $GLOBALS['baseUrl']
		];
	}
}