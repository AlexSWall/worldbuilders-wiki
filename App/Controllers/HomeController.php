<?php

namespace App\Controllers;

class HomeController extends Controller
{
	public function index($request, $response)
	{
		//return $this->view->render($response, 'core/home.twig');
		return $this->view->render($response, 'wiki/index.twig', [ 'wiki' => [
			'page_name' => 'Home',
			'title' => 'Home',
			'webpage_content' => \App\Models\Webpage::where('page_name', 'Home')->first()['webpage']
		]]);
	}
}