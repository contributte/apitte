<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Schema\Endpoint;
use ReflectionObject;

abstract class BasicEntity extends AbstractEntity
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
	 * @param ApiRequest $request
	 * @return static
	 */
	public function fromRequest(ApiRequest $request)
	{
		if (in_array($request->getMethod(), [Endpoint::METHOD_POST, Endpoint::METHOD_PUT, Endpoint::METHOD_PATCH])) {
			return $this->fromBodyRequest($request);
		}

		if ($request->getMethod() === Endpoint::METHOD_GET) {
			return $this->fromGetRequest($request);
		}

		return NULL;
	}

	/**
	 * @param array $data
	 * @return static
	 */
	public function factory(array $data)
	{
		$inst = new static();

		// Fill properties with real data
		$properties = $inst->getProperties();
		foreach ($properties as $property) {
			if (!array_key_exists($property['name'], $data)) continue;

			$value = $data[$property['name']];

			// Normalize & convert value (only not null values)
			if ($value !== NULL) {
				$value = $this->normalize($property['name'], $value);
			}

			// Fill single property
			$inst->{$property['name']} = $value;
		}

		return $inst;
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

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($property, $value)
	{
		return $value;
	}

	/**
	 * @param ApiRequest $request
	 * @return static
	 */
	protected function fromBodyRequest(ApiRequest $request)
	{
		return $this->factory($request->getJsonBody(TRUE));
	}

	/**
	 * @param ApiRequest $request
	 * @return static
	 */
	protected function fromGetRequest(ApiRequest $request)
	{
		return $this->factory($request->getQueryParams());
	}

}
