<?php

namespace App\Controllers;

class NotFoundController extends Controller
{
	static $logger;

	public function dealWithRequest($request, $response)
	{
		$response = new \Slim\Http\Response(404);
		$requestPath = substr($request->getUri()->getPath(), 1);

		self::$logger->addInfo('Page not found: ' . $requestPath);

		$args = [ 'page_name' => 'Page_Not_Found', 'requestPath' => $requestPath ];

		if ( strpos($requestPath, '/') )
			/* Has a / in the request path */
			return $this->view->render($response, 'core/pagenotfound.twig', [ 'requestPath' => $requestPath . 'asd' ] );	
		else
			return (new WikiController($this->container))->serveWebpage($request, $response, $args);
	}
}