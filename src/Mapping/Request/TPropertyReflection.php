<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Schema\Endpoint;
use ReflectionObject;

trait TPropertyReflection
{

	/**
	 * @return array
	 */
	public function getProperties()
	{
		if (!$this->properties) {
			$properties = [];
			$rf = new ReflectionObject($this);
			$class = get_class($this);

			foreach ($rf->getProperties() as $property) {
				// If property is not from the latest child, then skip it.
				if ($property->getDeclaringClass()->getName() !== $class) continue;

				// If property is not public, then skip it.
				if (!$property->isPublic()) continue;

				$properties[$property->getName()] = $property->getName();
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
		if (in_array($request->getMethod(), [Endpoint::METHOD_POST, Endpoint::METHOD_PUT])) {
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
			if (!array_key_exists($property, $data)) continue;

			// Fill single property
			$inst->{$property} = $data[$property];
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
			if (!isset($this->{$property})) continue;

			$data[$property] = $this->{$property};
		}

		return $data;
	}

	/**
	 * HELPERS *****************************************************************
	 */

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
