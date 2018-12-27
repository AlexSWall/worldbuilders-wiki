<?php

namespace App\Middleware;

abstract class Middleware
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public abstract function __invoke($request, $response, $next);
}