<?php

namespace Apitte\Core\Schema\Builder;

final class SchemaController
{

	/** @var string */
	private $class;

	/** @var string */
	private $rootPath;

	/** @var SchemaMethod[] */
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
	 * @return SchemaMethod
	 */
	public function addMethod($name)
	{
		$method = new SchemaMethod($name);
		$this->methods[$name] = $method;

		return $method;
	}

	/**
	 * @return SchemaMethod[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

}
