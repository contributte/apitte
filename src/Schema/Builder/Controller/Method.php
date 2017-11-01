<?php

namespace Apitte\Core\Schema\Builder\Controller;

final class Method
{

	/** @var string */
	private $name;

	/** @var string */
	private $path;

	/** @var string */
	private $id;

	/** @var string */
	private $description;

	/** @var string[] */
	private $methods = [];

	/** @var string[] */
	private $arguments = [];

	/** @var MethodParameter[] */
	private $parameters = [];

	/** @var MethodNegotiation[] */
	private $negotiations = [];

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
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->description = $description;
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
	public function addMethods(array $methods)
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
	public function addArgument($name, $type)
	{
		$this->arguments[$name] = $type;
	}

	/**
	 * @param mixed[] $arguments
	 * @return void
	 */
	public function addArguments(array $arguments)
	{
		foreach ($arguments as $type => $name) {
			$this->addArgument($type, $name);
		}
	}

	/**
	 * @return string[]
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * @param string $name
	 * @return MethodParameter
	 */
	public function addParameter($name)
	{
		$parameter = new MethodParameter($name);
		$this->parameters[$name] = $parameter;

		return $parameter;
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
	 * @return MethodParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @return MethodNegotiation
	 */
	public function addNegotiation()
	{
		$negotiation = new MethodNegotiation();
		$this->negotiations[] = $negotiation;

		return $negotiation;
	}

	/**
	 * @return MethodNegotiation[]
	 */
	public function getNegotiations()
	{
		return $this->negotiations;
	}

}
