<?php

namespace Apitte\Core\Schema\Builder\Controller;

final class Controller
{

	/** @var string */
	private $class;

	/** @var Method[] */
	private $methods = [];

	/** @var string */
	private $id;

	/** @var string */
	private $path;

	/** @var array */
	private $groupIds = [];

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
	 * @return Method[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * @param string $name
	 * @return Method
	 */
	public function addMethod($name)
	{
		$method = new Method($name);
		$this->methods[$name] = $method;

		return $method;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return array
	 */
	public function getGroupIds()
	{
		return $this->groupIds;
	}

	/**
	 * @param array $ids
	 * @return void
	 */
	public function setGroupIds(array $ids)
	{
		$this->groupIds = $ids;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function addGroupId($id)
	{
		$this->groupIds[] = $id;
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
