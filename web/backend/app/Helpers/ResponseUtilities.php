<?php

namespace App\Helpers;

class ResponseUtilities
{
	public static function respondWithError($logger, $response, $errorCode, $error)
	{
		$logger->addInfo('Returning error (Status Code ' . $errorCode . '): ' . $error);

		return $response->withStatus($errorCode)->withJSON([
				'error' => $error
		]);
	}
}
