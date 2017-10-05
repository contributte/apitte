<?php

namespace Apitte\Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceCallback
{

	/** @var object */
	private $service;

	/** @var string */
	private $method;

	/** @var array */
	private $arguments = [];

	/**
	 * @param object $service
	 * @param string $method
	 * @param array $args
	 */
	public function __construct($service, $method, array $args = [])
	{
		$this->service = $service;
		$this->method = $method;
		$this->arguments = $args;
	}

	/**
	 * @return object
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * @param array $args
	 * @return void
	 */
	public function setArguments(array $args)
	{
		$this->arguments = $args;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
	{
		return call_user_func_array([$this->service, $this->method], $this->arguments);
	}

}
