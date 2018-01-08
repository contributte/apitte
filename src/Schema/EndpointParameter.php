<?php

namespace Apitte\Core\Schema;

use Apitte\Core\Annotation\Controller\RequestParameter;

final class EndpointParameter
{

	const TYPE_SCALAR = 1;
	const TYPE_STRING = 2;
	const TYPE_INTEGER = 3;
	const TYPE_FLOAT = 4;
	const TYPE_BOOLEAN = 5;
	const TYPE_DATETIME = 6;
	const TYPE_OBJECT = 7;

	/** @var string */
	private $name;

	/** @var int */
	private $type = self::TYPE_SCALAR;

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
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $type
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
