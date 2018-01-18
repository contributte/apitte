<?php

namespace Apitte\Core\Mapping;

use ReflectionObject;

trait TReflectionProperties
{

	/** @var array */
	protected $properties = [];

	/**
	 * @return array
	 */
	public function getProperties()
	{
		if (!$this->properties) {
			$properties = [];
			$rf = new ReflectionObject($this);
			$class = get_class($this);

			$defaultProperties = $rf->getDefaultProperties();
			foreach ($rf->getProperties() as $property) {
				// If property is not from the latest child, then skip it.
				if ($property->getDeclaringClass()->getName() !== $class) continue;

				// If property is not public, then skip it.
				if (!$property->isPublic()) continue;

				$properties[$property->getName()] = [
					'name' => $property->getName(),
					'type' => $property->getValue($this),
					'defaultValue' => isset($defaultProperties[$property->getName()]) ? $defaultProperties[$property->getName()] : NULL,
				];
			}

			$this->properties = $properties;
		}

		return $this->properties;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		$data = [];
		$properties = $this->getProperties();

		foreach ($properties as $property) {
			if (!isset($this->{$property['name']})) continue;

			$data[$property['name']] = $this->{$property['name']};
		}

		return $data;
	}

}
