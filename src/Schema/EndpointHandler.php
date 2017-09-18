<?php

namespace Apitte\Core\Schema;

final class EndpointHandler
{

	/** @var string */
	private $class;

	/** @var string */
	private $method;

	/** @var string */
	private $callback;

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
	 * @return string
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param string $callback
	 * @return void
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

}
