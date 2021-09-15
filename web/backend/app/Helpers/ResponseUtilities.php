<?php

declare(strict_types=1);

namespace App\Helpers;

use Slim\Http\Response;

class ResponseUtilities
{
	public static \App\Logging\Logger $logger;

	public static function respondWithError( Response $response, int $errorCode, string $error, array $extraErrorData = [] ): Response
	{
		self::$logger->info( 'Returning error (Status Code ' . $errorCode . '): ' . $error );

		return $response->withStatus( $errorCode )->withJSON( array_merge( [
				'error' => $error
		], $extraErrorData ) );
	}
}
