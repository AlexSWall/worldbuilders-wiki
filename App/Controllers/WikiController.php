<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	public function getWebpage($name)
	{
		return Webpage::retrieveWebpageByName($name)->getWebpageRaw();
	}

	public function serveWebpage($request, $response, $args)
	{
		$page_name = $args['page_name'];
		$title = str_replace('_', ' ', $page_name);
		$webpage_content = $this->getWebpage($page_name);

		if ( is_null($webpage_content) )
			throw new \Slim\Exception\NotFoundException($request, $response);

		$arg_names = array( 'page_name', 'title', 'webpage_content' );
		$arg_map = array();
		foreach( $arg_names as $arg )
			$arg_map[$arg] = ${$arg};

		return $this->view->render($response, 'wiki/index.twig', ['wiki' => $arg_map]);
	}
}