<?php

namespace Apitte\Core\Schema\Builder;

final class SchemaController
{

	/** @var string */
	private $class;

	/** @var string */
	private $rootPath;

	/** @var SchemaControllerMethod[] */
	private $methods = [];

	/**
	 * @param string $class
	 */
	public function __construct($class)
	{
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @return string
	 */
	public function getRootPath()
	{
		return $this->rootPath;
	}

	/**
	 * @param string $rootPath
	 * @return void
	 */
	public function setRootPath($rootPath)
	{
		$this->rootPath = $rootPath;
	}

	/**
	 * @param string $name
	 * @return SchemaControllerMethod
	 */
	public function addMethod($name)
	{
		$method = new SchemaControllerMethod($name);
		$this->methods[$name] = $method;

		return $method;
	}

	/**
	 * @return SchemaControllerMethod[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

}
