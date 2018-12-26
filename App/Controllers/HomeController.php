<?php

namespace App\Controllers;

class HomeController extends Controller
{
	public function index($request, $response)
	{
		$this->flash->addMessage('info', 'Flash messages working!');
		$this->flash->addMessage('error', 'No error!');
		return $this->view->render($response, 'home.twig');
	}
}