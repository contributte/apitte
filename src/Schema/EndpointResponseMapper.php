<?php

namespace Apitte\Core\Schema;

final class EndpointResponseMapper
{

	/** @var string */
	private $entity;

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

}
