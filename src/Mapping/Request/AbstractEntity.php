<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Mapping\Arrayable;

abstract class AbstractEntity implements IRequestEntity, Arrayable
{

	/** @var array */
	protected $properties = [];

	/**
	 * @param array $data
	 * @return static
	 */
	public static function factory(array $data)
	{
		$inst = new static();

		// Fill properties with real data
		$properties = $inst->getProperties();
		foreach ($properties as $property) {
			if (!array_key_exists($property, $data)) continue;

			// Fill single property
			$inst->{$property} = $data[$property];
		}

		return $inst;
	}

	/**
	 * API *********************************************************************
	 */

	/**
	 * @return array
	 */
	abstract public function getProperties();

}
