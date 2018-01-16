<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Mapping\Arrayable;

abstract class AbstractEntity implements IRequestEntity, Arrayable
{

	/** @var array */
	protected $properties = [];

	/**
	 * @return array
	 */
	abstract public function getProperties();

}
