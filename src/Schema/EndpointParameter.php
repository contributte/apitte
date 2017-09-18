<?php

namespace Apitte\Core\Schema;

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

}
