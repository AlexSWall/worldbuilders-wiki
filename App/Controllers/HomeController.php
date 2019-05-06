<?php

namespace App\Controllers;

class HomeController extends Controller
{
	public function index($request, $response)
	{
		//return $this->view->render($response, 'core/home.twig');
		return $this->view->render($response, 'wiki/index.twig', [ 'wiki' => [
			'pageName' => 'Home',
			'title' => 'Home',
			'webpageContent' => \App\Models\Webpage::retrieveWebpageByName('Home')->getWebpageHTML()
		]]);
	}
}