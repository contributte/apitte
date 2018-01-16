<?php

namespace Apitte\Core\Schema;

final class EndpointRequestMapper
{

	/** @var string */
	private $entity;

	/** @var boolean */
	private $validation = TRUE;

	/**
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @param string $entity
	 * @return void
	 */
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}

	/**
	 * @return bool
	 */
	public function isValidation()
	{
		return $this->validation;
	}

	/**
	 * @param bool $validation
	 * @return void
	 */
	public function setValidation($validation)
	{
		$this->validation = boolval($validation);
	}

}
