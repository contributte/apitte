<?php

namespace Apitte\Core\Mapping\Request;

abstract class AbstractEntity implements IRequestEntity
{

	/** @var array */
	protected $properties = [];

	/**
	 * @param array $data
	 * @return static
	 */
	protected function createInstance(array $data)
	{
		$inst = new static();

		// Fill properties with real data
		$properties = $this->getProperties();
		foreach ($properties as $property) {
			if (!array_key_exists($property, $data)) continue;

			// Fill single property
			$inst->{$property} = $data[$property];
		}

		return $inst;
	}

}
