<?php

namespace App\Middleware;

class OldInputMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		if ( isset($_SESSION['old']) )
			$this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']); /* For use in old form data, e.g. signin's old.email field */

		$_SESSION['old'] = $request->getParams();

		$response = $next($request, $response);
		return $response;
	}
}