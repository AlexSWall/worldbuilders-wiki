<?php

namespace App\Controllers;

class HomeController extends Controller
{
	public function index($request, $response)
	{
		return (new WikiController($this->container))->serveWebpage($request, $response, ['page_name' => 'Home']);
	}
}