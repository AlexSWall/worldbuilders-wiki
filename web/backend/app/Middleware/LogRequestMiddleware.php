<?php declare( strict_types = 1 );

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Http\ServerRequest as Request;

class LogRequestMiddleware extends Middleware
{
	static \App\Logging\Logger $logger;

	public function route(Request $request, RequestHandlerInterface $handler): ResponseInterface
	{
		self::$logger->info('Request\'s Method Requested URI Path: '
			. $request->getMethod() . ': '
			. $request->getURI()->getPath());

		self::$logger->info('Request\'s Query Parameters: ' .
			json_encode($request->getQueryParams()));

		self::$logger->info('Request\'s POST Parameters: ' .
			json_encode(self::getCleanedPOSTParameters($request->getParsedBody())));

		self::$logger->info('Request\'s Cookie Parameters: ' . 
			json_encode($request->getCookieParams()));

		$response = $handler->handle($request);

		self::$logger->info('Response\'s Status Code: ' .
			json_encode($response->getStatusCode()));

		self::$logger->info('Response\'s Headers: ' .
			json_encode($response->getHeaders()));

		self::$logger->info('Response\'s Body: ' .
			json_encode($response->getBody()));

		return $response;
	}

	/* Redact any information which is keyed with a key which contains the phrase 'password'. */
	private static function getCleanedPOSTParameters(?array $restParams): array
	{
		if ($restParams === null)
			return [];

		foreach ($restParams as $key => $value)
			if ( is_string($value) && (stristr($value, 'password') || stristr($key, 'password') ) )
				$restParams[$key] = '<REDACTED>';
			elseif ( is_array($value) )
				$restParams[$key] = self::getCleanedPOSTParameters($value);
		return $restParams;
	}
}
