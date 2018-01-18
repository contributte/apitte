<?php

namespace Apitte\Core\Mapping\Response;

use Apitte\Core\Mapping\TReflectionProperties;

abstract class BasicEntity extends AbstractEntity
{

	use TReflectionProperties;

	/**
	 * @return array
	 */
	public function getResponseProperties()
	{
		return $this->getProperties();
	}

	/**
	 * @return array
	 */
	public function toResponse()
	{
		return $this->toArray();
	}

}
