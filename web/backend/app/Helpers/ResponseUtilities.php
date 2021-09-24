<?php

declare(strict_types=1);

namespace App\Helpers;

use Slim\Psr7\Response as PsrResponse;

use Psr\Http\Message\ResponseInterface;

class ResponseUtilities
{
	public static \App\Logging\Logger $logger;

	public static function getApiResponse(
		?ResponseInterface $response,
		int $statusCode,
		array $responseData
	): ResponseInterface
	{
		self::$logger->info( 'Returning API response (status code ' . $statusCode . ')' );

		if ( $response === null )
		{
			$response = new PsrResponse();
		}

		$response->getBody->write( json_encode( $responseData ) );

		return $response->withStatus( $statusCode );
	}

	public static function getApiErrorResponse(
		?ResponseInterface $response,
		int $errorCode,
		string $error,
		array $extraErrorData = []
	): ResponseInterface
	{
		self::$logger->info( 'Returning error (status code ' . $errorCode . '): ' . $error );

		if ( $response === null )
		{
			$response = new PsrResponse();
		}

		$response->getBody->write( json_encode( array_merge( [
			'error' => $error
		], $extraErrorData ) ) );

		return $response->withStatus( $errorCode );
	}

	public static function respondWithRedirect(
		?ResponseInterface $response,
		string $location
	): ResponseInterface
	{
		self::$logger->info( 'Returning redirect to ' . $location );

		if ( $response === null )
		{
			$response = new PsrResponse();
		}

		return ( new PsrResponse() )->withHeader(
			'Location',
			$location
		)->withStatus( 302 );
	}
}
