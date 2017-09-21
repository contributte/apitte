<?php

namespace Apitte\Core\Schema\Builder;

final class SchemaControllerMethod
{

	/** @var string */
	private $name;

	/** @var string */
	private $path;

	/** @var string[] */
	private $methods = [];

	/** @var string[] */
	private $arguments = [];

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

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
		$this->methods = $methods;
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function addMethod($method)
	{
		$this->methods[] = strtoupper($method);
	}

	/**
	 * @param string|string[] $methods
	 * @return void
	 */
	public function appendMethods(array $methods)
	{
		foreach ($methods as $method) {
			$this->addMethod($method);
		}
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return void
	 */
	public function appendArgument($name, $type)
	{
		$this->arguments[$name] = $type;
	}

	/**
	 * @return string[]
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

}
