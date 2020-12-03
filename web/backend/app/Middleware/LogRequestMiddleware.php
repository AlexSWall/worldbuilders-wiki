<?php

namespace App\Middleware;

class LogRequestMiddleware extends Middleware
{
	static $logger;

	public function __invoke($request, $response, $next)
	{
		self::$logger->addInfo('Request\'s Method Requested URI Path: '
			. $request->getMethod() . ': '
			. $request->getURI()->getPath());

		self::$logger->addInfo('Request\'s Query Parameters: ' .
			json_encode($request->getQueryParams()));

		self::$logger->addInfo('Request\'s POST Parameters: ' .
			json_encode($this->getCleanedPOSTParameters($request->getParsedBody())));

		self::$logger->addInfo('Request\'s Cookie Parameters: ' . 
			json_encode($request->getCookieParams()));

		$response = $next($request, $response);
		return $response;
	}

	/* Redact any information which is keyed with a key which contains the phrase 'password'. */
	private function getCleanedPOSTParameters($restParams)
	{
		if (is_null($restParams))
			return [];

		foreach ($restParams as $key => $value)
			if ( stristr($value, 'password') )
				$restParams[$key] = '<REDACTED>';
		return $restParams;
	}
}
