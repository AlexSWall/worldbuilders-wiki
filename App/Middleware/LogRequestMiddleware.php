<?php

namespace App\Middleware;

class LogRequestMiddleware extends Middleware
{
	static $logger;

	public function __invoke($request, $response, $next)
	{
		self::$logger->addInfo('Request path: ' . $request->getURI()->getPath());
		self::$logger->addInfo('Request parameters: ' . json_encode($request->getParams()));
		$response = $next($request, $response);
		return $response;
	}
}