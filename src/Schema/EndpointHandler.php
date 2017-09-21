<?php

namespace Apitte\Core\Schema;

final class EndpointHandler
{

	/** @var string */
	private $class;

	/** @var string */
	private $method;

	/** @var array */
	private $arguments = [];

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 * @return void
	 */
	public function setClass($class)
	{
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * @param array $arguments
	 * @return void
	 */
	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;
	}

}
