<?php

namespace App\Helpers;

class ResponseUtilities
{
	static $logger;

	public static function respondWithError($response, $errorCode, $error, $extraErrorData = [])
	{
		self::$logger->addInfo('Returning error (Status Code ' . $errorCode . '): ' . $error);

		return $response->withStatus($errorCode)->withJSON(array_merge([
				'error' => $error
		], $extraErrorData));
	}
}
