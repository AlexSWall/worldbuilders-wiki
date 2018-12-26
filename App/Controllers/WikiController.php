<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	public function getWebpage($name)
	{
		return Webpage::where('page_name', $name)->first()['webpage'];
	}

	public function serveWebpage($request, $response, $args)
	{
		$webpage_content = $this->getWebpage($args['name']);

		if ( !is_null($webpage_content) )
			return $this->view->render($response, 'wikipage.twig', [
				'webpage' => $webpage_content
			]);
		else
			throw new \Slim\Exception\NotFoundException($request, $response);
	}
}