<?php

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Parameter\ITypeMapper;
use Apitte\Core\Schema\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestParameterMapping
{

	/** @var ITypeMapper[] */
	protected $types = [];

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * @param string $type
	 * @param string|ITypeMapper $mapper
	 * @return void
	 */
	public function addMapper($type, $mapper)
	{
		$this->types[$type] = $mapper;
	}

	/**
	 * MAPPING *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface|ServerRequestInterface
	 */
	public function map(ServerRequestInterface $request, ResponseInterface $response)
	{
		/** @var Endpoint $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// Get all parameters
		$parameters = $endpoint->getParameters();

		// Skip, if there are no parameters
		if (!$parameters) return $request;

		// Get request parameters from attribute
		$requestParameters = $request->getAttribute(RequestAttributes::ATTR_PARAMETERS);

		// Iterate over all parameters
		foreach ($parameters as $parameter) {
			$mapper = $this->getMapper($parameter->getType());

			// If it's unsupported type, skip it
			if (!$mapper) continue;

			// Obtain request parameter values
			$value = $requestParameters[$parameter->getName()];

			// Normalize value
			$normalizedValue = $mapper->normalize($value);

			// Update requests
			$requestParameters[$parameter->getName()] = $normalizedValue;
			$request = $request->withAttribute(RequestAttributes::ATTR_PARAMETERS, $requestParameters);
		}

		return $request;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param string $type
	 * @return ITypeMapper
	 */
	protected function getMapper($type)
	{
		if (!isset($this->types[$type])) return NULL;

		// Initialize mapper
		if (!is_object($this->types[$type])) {
			$this->types[$type] = new $this->types[$type];
		}

		return $this->types[$type];
	}

}
