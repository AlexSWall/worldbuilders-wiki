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

		$args = [ 'pageName' => 'Page_Not_Found', 'requestPath' => $requestPath ];

		return (new WikiController($this->container))->serveWikiContent($request, $response, $args);
	}
}