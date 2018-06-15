<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Logical;

use Apitte\Core\Exception\LogicalException;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;

final class InvalidSchemaException extends LogicalException
{

	/** @var Controller */
	public $controller;

	/** @var Method */
	public $method;

	/**
	 * @return static
	 */
	public function withController(Controller $controller)
	{
		$this->controller = $controller;

		return $this;
	}

	/**
	 * @return static
	 */
	public function withMethod(Method $method)
	{
		$this->method = $method;

		return $this;
	}

}
