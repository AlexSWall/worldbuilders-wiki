<?php

namespace App\Middleware;

class OldInputMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if ( isset($_SESSION['params']) )
			$GLOBALS['previous_params'] = $_SESSION['params'];

		$_SESSION['params'] = $request->getParams();

		$response = $next($request, $response);
		return $response;
	}
}