<?php

namespace Apitte\Core\Schema\Builder;

final class SchemaBuilder
{

	/** @var SchemaController[] */
	private $controllers = [];

	/**
	 * @param string $class
	 * @return SchemaController
	 */
	public function addController($class)
	{
		$controller = new SchemaController($class);
		$this->controllers[$class] = $controller;

		return $controller;
	}

	/**
	 * @return SchemaController[]
	 */
	public function getControllers()
	{
		return $this->controllers;
	}

}
