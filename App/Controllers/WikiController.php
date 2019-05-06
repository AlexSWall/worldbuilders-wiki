<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	public function serveWebpage($request, $response, $args)
	{
		$pageName = $args['page_name'];
		$webpage = Webpage::retrieveWebpageByName($pageName);

		if ( is_null($webpage) )
			throw new \Slim\Exception\NotFoundException($request, $response);

		$title = str_replace('_', ' ', $pageName);
		$webpageContent = $webpage->getWebpageHTML($pageName);

		$argNames = array( 'pageName', 'title', 'webpageContent' );
		$argMap = array();
		foreach( $argNames as $arg )
			$argMap[$arg] = ${$arg};

		return $this->view->render($response, 'wiki/index.twig', ['wiki' => $argMap]);
	}
}