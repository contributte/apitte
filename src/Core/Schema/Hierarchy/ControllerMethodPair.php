<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Hierarchy;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;

class ControllerMethodPair
{

	/** @var Controller */
	private $controller;

	/** @var Method */
	private $method;

	public function __construct(Controller $controller, Method $method)
	{
		$this->controller = $controller;
		$this->method = $method;
	}

	public function getController(): Controller
	{
		return $this->controller;
	}

	public function getMethod(): Method
	{
		return $this->method;
	}

}
