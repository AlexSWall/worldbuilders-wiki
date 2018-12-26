<?php

namespace App\Controllers;

use App\Models\Webpage;

class WikiController extends Controller
{
	public function getWebpage($name)
	{
		return Webpage::where('page_name', $name)->first()['webpage'];
	}

	public function serveWebpage($request, $response)
	{
		if ( true )
		{

		}
		$requestPath = $request->getUri()->getPath();

		$response = new \Slim\Http\Response(404);
		return $response->write("Page not found");
	}
}