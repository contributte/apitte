<?php

namespace Apitte\Core\Schema\Builder;

use Apitte\Core\Schema\Builder\Controller\Controller;

final class SchemaBuilder
{

	/** @var Controller[] */
	private $controllers = [];

	/**
	 * @param string $class
	 * @return Controller
	 */
	public function addController($class)
	{
		$controller = new Controller($class);
		$this->controllers[$class] = $controller;

		return $controller;
	}

	/**
	 * @return Controller[]
	 */
	public function getControllers()
	{
		return $this->controllers;
	}

}
