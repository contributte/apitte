<?php

namespace Apitte\Core\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;

final class Endpoint
{

	// Methods
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTION = 'OPTION';

	const METHODS = [
		'GET',
		'POST',
		'PUT',
		'DELETE',
		'OPTION',
	];

	/** @var string[] */
	private $methods = [];

	/** @var string */
	private $mask;

	/** @var string */
	private $pattern;

	/** @var EndpointHandler */
	private $handler;

	/** @var EndpointParameter[] */
	private $parameters = [];

	/**
	 * @return string[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * @param string[] $methods
	 * @return void
	 */
	public function setMethods(array $methods)
	{
		foreach ($methods as $method) {
			$this->addMethod($method);
		}
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function addMethod($method)
	{
		$method = strtoupper($method);

		if (!in_array($method, self::METHODS)) {
			throw new InvalidArgumentException(sprintf('Method %s is not allowed', $method));
		}

		$this->methods[] = $method;
	}

	/**
	 * @param string $method
	 * @return bool
	 */
	public function hasMethod($method)
	{
		return in_array(strtoupper($method), $this->methods);
	}

	/**
	 * @return string
	 */
	public function getMask()
	{
		return $this->mask;
	}

	/**
	 * @param string $mask
	 * @return void
	 */
	public function setMask($mask)
	{
		$this->mask = $mask;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * @param string $pattern
	 * @return void
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * @return EndpointHandler
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * @param EndpointHandler $handler
	 * @return void
	 */
	public function setHandler($handler)
	{
		$this->handler = $handler;
	}

	/**
	 * @return EndpointParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * @param EndpointParameter $param
	 * @return void
	 */
	public function addParameter(EndpointParameter $param)
	{
		$this->parameters[$param->getName()] = $param;
	}

	/**
	 * @param EndpointParameter[] $parameters
	 * @return void
	 */
	public function setParameters(array $parameters)
	{
		foreach ($parameters as $param) {
			$this->addParameter($param);
		}
	}

}
