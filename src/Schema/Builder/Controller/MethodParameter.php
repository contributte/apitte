<?php

namespace Apitte\Core\Schema\Builder\Controller;

use Apitte\Core\Annotation\Controller\RequestParameter;

final class MethodParameter
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var string */
	private $description;

	/** @var string */
	private $in = RequestParameter::IN_PATH;

	/** @var bool */
	private $required = TRUE;

	/** @var bool */
	private $deprecated = FALSE;

	/** @var bool */
	private $allowEmpty = FALSE;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

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
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
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
	 * @return string
	 */
	public function getIn()
	{
		return $this->in;
	}

	/**
	 * @param string $in
	 * @return void
	 */
	public function setIn($in)
	{
		$this->in = $in;
	}

	/**
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @param bool $required
	 * @return void
	 */
	public function setRequired($required)
	{
		$this->required = $required;
	}

	/**
	 * @return bool
	 */
	public function isDeprecated()
	{
		return $this->deprecated;
	}

	/**
	 * @param bool $deprecated
	 * @return void
	 */
	public function setDeprecated($deprecated)
	{
		$this->deprecated = $deprecated;
	}

	/**
	 * @return bool
	 */
	public function isAllowEmpty()
	{
		return $this->allowEmpty;
	}

	/**
	 * @param bool $allowEmpty
	 * @return void
	 */
	public function setAllowEmpty($allowEmpty)
	{
		$this->allowEmpty = $allowEmpty;
	}

}
