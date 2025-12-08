<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Hierarchy;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;

class ControllerMethodPair
{

	public function __construct(
		private readonly Controller $controller,
		private readonly Method $method,
	)
	{
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
