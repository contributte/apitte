<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Parameter\ITypeMapper;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestParameterMapping
{

	/** @var string[] */
	protected static $exceptions = [
		InvalidArgumentTypeException::TYPE_INTEGER => '%s parameter "%s" should be of type integer.',
		InvalidArgumentTypeException::TYPE_FLOAT => '%s parameter "%s" should be of type float or integer.',
		InvalidArgumentTypeException::TYPE_BOOLEAN => '%s parameter "%s" should be of type boolean. Pass "true" or "1" for true and "false" or "0" for false.',
		InvalidArgumentTypeException::TYPE_DATETIME => '%s parameter "%s" should be of type datetime in format ISO 8601 (Y-m-d\TH:i:sP).',
	];

	/** @var string[]|ITypeMapper[] */
	protected $types = [];

	/**
	 * @param string|ITypeMapper $mapper
	 */
	public function addMapper(string $type, $mapper): void
	{
		if (!is_subclass_of($mapper, ITypeMapper::class)) {
			throw new InvalidArgumentException(sprintf('Mapper must be string representation or instance of %s.', ITypeMapper::class));
		}

		$this->types[$type] = $mapper;
	}

	/**
	 * @param ServerRequestInterface|ApiRequest $request
	 */
	public function map(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// Get all parameters
		$parameters = $endpoint->getParameters();

		// Skip, if there are no parameters
		if (!$parameters) return $request;

		$headerParameters = $request->getHeaders();
		$cookieParams = $request->getCookieParams();
		// Get request parameters from attribute
		$requestParameters = $request->getAttribute(RequestAttributes::ATTR_PARAMETERS);

		// Iterate over all parameters
		foreach ($parameters as $parameter) {
			$mapper = $this->getMapper($parameter->getType());

			// If it's unsupported type, skip it
			if (!$mapper) continue;

			switch ($parameter->getIn()) {
				case $parameter::IN_PATH:
				case $parameter::IN_QUERY:
					// Logical check
					if (!array_key_exists($parameter->getName(), $requestParameters)) {
						if (!$parameter->isRequired()) {
							break;
						}

						throw new ClientErrorException(sprintf('Parameter "%s" should be provided in request attributes', $parameter->getName()));
					}

					// Obtain request parameter values
					$value = $requestParameters[$parameter->getName()];

					if ($value === null && $parameter->isRequired()) {
						throw new ClientErrorException(sprintf('Parameter "%s" should be provided in request attributes', $parameter->getName()));
					}
					if ($value === '' && !$parameter->isAllowEmpty()) {
						throw new ClientErrorException(sprintf('Parameter "%s" should not be empty', $parameter->getName()));
					}

					// Normalize value
					$normalizedValue = $this->normalize($value, $parameter, $mapper);

					// Update requests
					$requestParameters[$parameter->getName()] = $normalizedValue;
					$request = $request->withAttribute(RequestAttributes::ATTR_PARAMETERS, $requestParameters);

					break;
				case $parameter::IN_COOKIE:
					// Logical check
					if (!array_key_exists($parameter->getName(), $cookieParams)) {
						if (!$parameter->isRequired()) {
							break;
						}

						throw new ClientErrorException(sprintf('Parameter "%s" should be provided in request cookies', $parameter->getName()));
					}

					// Obtain request parameter values
					$value = $cookieParams[$parameter->getName()];

					if ($value === null && $parameter->isRequired()) {
						throw new ClientErrorException(sprintf('Parameter "%s" should be provided in request attributes', $parameter->getName()));
					}
					if ($value === '' && !$parameter->isAllowEmpty()) {
						throw new ClientErrorException(sprintf('Parameter "%s" should not be empty', $parameter->getName()));
					}

					// Normalize value
					$normalizedValue = $this->normalize($value, $parameter, $mapper);

					// Update requests
					$cookieParams[$parameter->getName()] = $normalizedValue;
					$request = $request->withCookieParams($cookieParams);

					break;
				case $parameter::IN_HEADER:
					$headerParameterName = strtolower($parameter->getName());
					// Logical check
					if (!array_key_exists($headerParameterName, $headerParameters)) {
						if (!$parameter->isRequired()) {
							break;
						}

						throw new ClientErrorException(sprintf('Parameter "%s" should be provided in request header', $parameter->getName()));
					}

					// Obtain request parameter values
					$values = $headerParameters[$headerParameterName];
					$normalizedValues = [];

					// Normalize value
					foreach ($values as $index => $value) {
						if ($value === '' && !$parameter->isAllowEmpty()) {
							throw new ClientErrorException(sprintf('Parameter "%s" should not be empty', $parameter->getName()));
						}

						$normalizedValues[$index] = $this->normalize($value, $parameter, $mapper);
					}

					// Update requests
					$headerParameters[$headerParameterName] = $normalizedValues;
					$request = $request->withHeader($headerParameterName, $normalizedValues);

					break;
			}
		}

		return $request;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function normalize($value, EndpointParameter $parameter, ITypeMapper $mapper)
	{
		try {
			return $mapper->normalize($value);
		} catch (InvalidArgumentTypeException $e) {
			throw new ClientErrorException(sprintf(
				self::$exceptions[$e->getType()],
				ucfirst($parameter->getIn()),
				$parameter->getName()
			));
		}
	}

	protected function getMapper(string $type): ?ITypeMapper
	{
		if (!isset($this->types[$type])) return null;

		// Initialize mapper
		if (!is_object($this->types[$type])) {
			$this->types[$type] = new $this->types[$type]();
		}

		return $this->types[$type];
	}

}
