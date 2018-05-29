<?php

namespace Apitte\Core\Schema;

final class EndpointParameter
{

	const TYPE_SCALAR = 'scalar';
	const TYPE_STRING = 'string';
	const TYPE_INTEGER = 'int';
	const TYPE_FLOAT = 'float';
	const TYPE_BOOLEAN = 'bool';
	const TYPE_DATETIME = 'datetime';
	const TYPE_OBJECT = 'object';

	const IN_QUERY = 'query';
	const IN_COOKIE = 'cookie';
	const IN_HEADER = 'header';
	const IN_PATH = 'path';

	/** @var string */
	private $name;

	/** @var int */
	private $type = self::TYPE_SCALAR;

	/** @var string */
	private $description;

	/** @var string */
	private $in = self::IN_PATH;

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
		$this->required = boolval($required);
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
		$this->deprecated = boolval($deprecated);
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
		$this->allowEmpty = boolval($allowEmpty);
	}

}
