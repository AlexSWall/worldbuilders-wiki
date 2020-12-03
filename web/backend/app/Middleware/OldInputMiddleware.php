<?php

namespace App\Middleware;

use App\Globals\FrontEndParametersFacade;

class OldInputMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if ( isset($_SESSION['params']) )
			FrontEndParametersFacade::setPreviousParameters($_SESSION['params']);

		$_SESSION['params'] = $request->getParams();

		$response = $next($request, $response);
		return $response;
	}
}
