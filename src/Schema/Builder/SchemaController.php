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

	/** @var string */
	private $group;

	/** @var string[] */
	private $groupPaths = [];

	/** @var string[] */
	private $tags = [];

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
	 * @return SchemaControllerMethod[]
	 */
	public function getMethods()
	{
		return $this->methods;
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
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param string $group
	 * @return void
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * @return string[]
	 */
	public function getGroupPaths()
	{
		return $this->groupPaths;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function addGroupPath($path)
	{
		$this->groupPaths[] = $path;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function addTag($name, $value)
	{
		$this->tags[$name] = $value;
	}

	/**
	 * @return string[]
	 */
	public function getTags()
	{
		return $this->tags;
	}

}
