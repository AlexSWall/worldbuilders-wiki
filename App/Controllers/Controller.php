<?php

namespace App\Controllers;

abstract class Controller
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	/* e.g. give controllers access to view. */
	public function __get($property)
	{
		if ( $this->container->{$property} )
		{
			return $this->container->{$property};
		}
	}
}
