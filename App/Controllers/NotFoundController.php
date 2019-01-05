<?php

namespace App\Controllers;

use App\Models\Webpage;

class NotFoundController extends Controller
{
	public function dealWithRequest($request, $response)
	{
		$this->flash->addMessage('error', 'Page not found!');

		$response = new \Slim\Http\Response(404);
		$requestPath = substr($request->getUri()->getPath(), 1);
		if ( strpos($requestPath, '/') )
			/* Has a / in the request path */
			return $this->view->render($response, 'core/pagenotfound.twig', [ 'requestPath' => $requestPath . 'asd' ] );	
		else
			return $this->view->render($response, 'wiki/wikipagenotfound.twig', [ 'requestPath' => $requestPath ] );
	}
}