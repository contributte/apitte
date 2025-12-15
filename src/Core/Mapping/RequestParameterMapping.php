<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Parameter\ITypeMapper;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;

class RequestParameterMapping
{

	/** @var string[] */
	protected static array $exceptions = [
		InvalidArgumentTypeException::TYPE_INTEGER => '%s request parameter "%s" should be of type integer.',
		InvalidArgumentTypeException::TYPE_FLOAT => '%s request parameter "%s" should be of type float or integer.',
		InvalidArgumentTypeException::TYPE_BOOLEAN => '%s request parameter "%s" should be of type boolean. Pass "true" for true or "false" for false.',
		InvalidArgumentTypeException::TYPE_DATETIME => '%s request parameter "%s" should be of type datetime in format ISO 8601 (Y-m-d\TH:i:sP).',
		InvalidArgumentTypeException::TYPE_ENUM => '%s request parameter "%s" is not in the allowed values.',
	];

	protected static string $customException = '%s request parameter "%s" should be of type %s.%s';

	/** @var array<string, ITypeMapper|class-string<ITypeMapper>> */
	protected array $types = [];

	public function addMapper(string $type, ITypeMapper|string $mapper): void
	{
		if (!is_subclass_of($mapper, ITypeMapper::class)) {
			throw new InvalidArgumentException(sprintf('Mapper must be string representation or instance of %s.', ITypeMapper::class));
		}

		$this->types[$type] = $mapper;
	}

	public function map(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if ($endpoint === null) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// Get all parameters
		$parameters = $endpoint->getParameters();

		// Skip, if there are no parameters
		if ($parameters === []) {
			return $request;
		}

		$headerParameters = array_change_key_case($request->getHeaders(), CASE_LOWER);
		$cookieParams = $request->getCookieParams();
		// Get request parameters from attribute
		/** @var array<string, mixed> $requestParameters */
		$requestParameters = $request->getAttribute(RequestAttributes::ATTR_PARAMETERS);

		// Iterate over all parameters
		foreach ($parameters as $parameter) {
			$mapper = $this->getMapper($parameter->getType());

			// If it's unsupported type, skip it
			if ($mapper === null) {
				continue;
			}

			switch ($parameter->getIn()) {
				case $parameter::IN_PATH:
				case $parameter::IN_QUERY:
					// Logical check
					if (!array_key_exists($parameter->getName(), $requestParameters)) {
						if (!$parameter->isRequired()) {
							break;
						}

						throw new ClientErrorException(sprintf(
							'%s request parameter "%s" should be provided.',
							ucfirst($parameter->getIn()),
							$parameter->getName()
						));
					}

					// Obtain request parameter values
					$value = $requestParameters[$parameter->getName()];

					$this->checkParameterProvided($parameter, $value);
					$this->checkParameterNotEmpty($parameter, $value);

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

						throw new ClientErrorException(sprintf(
							'%s request parameter "%s" should be provided.',
							ucfirst($parameter->getIn()),
							$parameter->getName()
						));
					}

					// Obtain request parameter values
					$value = $cookieParams[$parameter->getName()];

					$this->checkParameterProvided($parameter, $value);
					$this->checkParameterNotEmpty($parameter, $value);

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

						throw new ClientErrorException(sprintf(
							'%s request parameter "%s" should be provided.',
							ucfirst($parameter->getIn()),
							$parameter->getName()
						));
					}

					// Obtain request parameter values
					$values = $headerParameters[$headerParameterName];
					$normalizedValues = [];

					// Normalize value
					foreach ($values as $index => $value) {
						$this->checkParameterNotEmpty($parameter, $value);

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

	public function getMapper(string $type): ?ITypeMapper
	{
		if (!isset($this->types[$type])) {
			return null;
		}

		// Initialize mapper
		if (!($this->types[$type] instanceof ITypeMapper)) {
			$this->types[$type] = new $this->types[$type]();
		}

		return $this->types[$type];
	}

	protected function checkParameterProvided(EndpointParameter $parameter, mixed $value): void
	{
		if ($value === null && $parameter->isRequired()) {
			throw new ClientErrorException(sprintf(
				'%s request parameter "%s" should be provided.',
				ucfirst($parameter->getIn()),
				$parameter->getName()
			));
		}
	}

	protected function checkParameterNotEmpty(EndpointParameter $parameter, mixed $value): void
	{
		if ($value === '' && !$parameter->isAllowEmpty()) {
			throw new ClientErrorException(sprintf(
				'%s request parameter "%s" should not be empty.',
				ucfirst($parameter->getIn()),
				$parameter->getName()
			));
		}
	}

	protected function normalize(mixed $value, EndpointParameter $parameter, ITypeMapper $mapper): mixed
	{
		if ($value === '' || $value === null) {
			return null;
		}

		try {
			return $mapper->normalize($value, ['endpoint' => $parameter]);
		} catch (InvalidArgumentTypeException $e) {
			if (array_key_exists($e->getType(), self::$exceptions)) {
				throw new ClientErrorException(sprintf(
					self::$exceptions[$e->getType()],
					ucfirst($parameter->getIn()),
					$parameter->getName()
				));
			} else {
				throw new ClientErrorException(sprintf(
					self::$customException,
					ucfirst($parameter->getIn()),
					$parameter->getName(),
					$e->getType(),
					$e->getDescription() !== null ? (' ' . $e->getDescription()) : ''
				));
			}
		}
	}

}
