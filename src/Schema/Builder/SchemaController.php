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

	/** @var array */
	private $groups = [];

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
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * @param array $groups
	 * @return void
	 */
	public function setGroups(array $groups)
	{
		$this->groups = $groups;
	}

	/**
	 * @param string $group
	 * @return void
	 */
	public function addGroup($group)
	{
		$this->groups[] = $group;
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
