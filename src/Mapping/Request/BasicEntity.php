<?php

namespace Apitte\Core\Mapping\Request;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Mapping\TReflectionProperties;
use Apitte\Core\Schema\Endpoint;

abstract class BasicEntity extends AbstractEntity
{

	use TReflectionProperties;

	/**
	 * @return array
	 */
	public function getRequestProperties()
	{
		return $this->getProperties();
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
		$properties = $inst->getRequestProperties();
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
