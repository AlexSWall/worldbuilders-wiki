<?php declare( strict_types = 1 );

namespace App\Controllers;

use Psr\Container\ContainerInterface;

abstract class Controller
{
	protected ContainerInterface $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/* E.g. gives Controllers access to view. */
	public function __get(string $property): mixed
	{
		if ( $this->container->get($property) )
		{
			return $this->container->get($property);
		}
	}
}
