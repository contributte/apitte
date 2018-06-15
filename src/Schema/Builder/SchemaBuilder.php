<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder;

use Apitte\Core\Schema\Builder\Controller\Controller;

final class SchemaBuilder
{

	/** @var Controller[] */
	private $controllers = [];

	public function addController(string $class): Controller
	{
		$controller = new Controller($class);
		$this->controllers[$class] = $controller;

		return $controller;
	}

	/**
	 * @return Controller[]
	 */
	public function getControllers(): array
	{
		return $this->controllers;
	}

}
