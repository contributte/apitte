<?php

namespace Apitte\Core\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\Utils\Arrays;

final class Endpoint
{

	// Methods
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_PATCH = 'PATCH';

	const METHODS = [
		self::METHOD_GET,
		self::METHOD_POST,
		self::METHOD_PUT,
		self::METHOD_DELETE,
		self::METHOD_OPTIONS,
		self::METHOD_PATCH,
	];

	// Tags
	const TAG_ID = 'id';
	const TAG_GROUP_IDS = 'group.ids';
	const TAG_GROUP_PATHS = 'group.paths';

	/** @var string[] */
	private $methods = [];

	/** @var string */
	private $mask;

	/** @var string */
	private $pattern;

	/** @var EndpointHandler */
	private $handler;

	/** @var string */
	private $description;

	/** @var EndpointParameter[] */
	private $parameters = [];

	/** @var EndpointNegotiation[] */
	private $negotiations = [];

	/** @var EndpointRequestMapper */
	private $requestMapper;

	/** @var EndpointResponseMapper */
	private $responseMapper;

	/** @var mixed[] */
	private $tags = [];

	/** @var array */
	private $metadata = [];

	/**
	 * @return string[]
	 */
	public function getMethods()
	{
		return $this->methods;
	}

	/**
	 * @param string[] $methods
	 * @return void
	 */
	public function setMethods(array $methods)
	{
		foreach ($methods as $method) {
			$this->addMethod($method);
		}
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function addMethod($method)
	{
		$method = strtoupper($method);

		if (!in_array($method, self::METHODS)) {
			throw new InvalidArgumentException(sprintf('Method %s is not allowed', $method));
		}

		$this->methods[] = $method;
	}

	/**
	 * @param string $method
	 * @return bool
	 */
	public function hasMethod($method)
	{
		return in_array(strtoupper($method), $this->methods);
	}

	/**
	 * @return string
	 */
	public function getMask()
	{
		return $this->mask;
	}

	/**
	 * @param string $mask
	 * @return void
	 */
	public function setMask($mask)
	{
		$this->mask = $mask;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		if (!$this->pattern) {
			$this->pattern = $this->generatePattern();
		}

		return $this->pattern;
	}

	/**
	 * @param string $pattern
	 * @return void
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * @return EndpointHandler
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * @param EndpointHandler $handler
	 * @return void
	 */
	public function setHandler(EndpointHandler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return EndpointParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * @param EndpointParameter $param
	 * @return void
	 */
	public function addParameter(EndpointParameter $param)
	{
		$this->parameters[$param->getName()] = $param;
	}

	/**
	 * @param EndpointParameter[] $parameters
	 * @return void
	 */
	public function setParameters(array $parameters)
	{
		foreach ($parameters as $param) {
			$this->addParameter($param);
		}
	}

	/**
	 * @return EndpointNegotiation[]
	 */
	public function getNegotiations()
	{
		return $this->negotiations;
	}

	/**
	 * @param EndpointNegotiation $negotiation
	 * @return void
	 */
	public function addNegotiation(EndpointNegotiation $negotiation)
	{
		$this->negotiations[] = $negotiation;
	}

	/**
	 * @param EndpointNegotiation[] $negotiations
	 * @return void
	 */
	public function setNegotiations($negotiations)
	{
		$this->negotiations = $negotiations;
	}

	/**
	 * @return array
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function getTag($name)
	{
		return $this->hasTag($name) ? $this->tags[$name] : NULL;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasTag($name)
	{
		return array_key_exists($name, $this->tags);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function addTag($name, $value)
	{
		$this->tags[$name] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		$this->metadata[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getAttribute($key, $default = NULL)
	{
		return Arrays::get($this->metadata, $key, $default);
	}

	/**
	 * @return string
	 */
	protected function generatePattern()
	{
		$rawPattern = $this->getAttribute('pattern', NULL);

		if ($rawPattern === NULL) {
			throw new InvalidStateException('Pattern attribute is required');
		}

		$suffixes = [];
		foreach ($this->getNegotiations() as $negotiation) {
			$suffix = $negotiation->getSuffix();

			// Skip if suffix is not provided
			if (!$suffix) continue;

			$suffixes[] = $suffix;
		}

		if ($suffixes) {
			return sprintf('#%s(?:%s)?$/?\z#A', $rawPattern, implode('|', $suffixes));
		} else {
			return sprintf('#%s$/?\z#A', $rawPattern);
		}
	}

	/**
	 * @return EndpointRequestMapper
	 */
	public function getRequestMapper()
	{
		return $this->requestMapper;
	}

	/**
	 * @param EndpointRequestMapper $requestMapper
	 * @return void
	 */
	public function setRequestMapper(EndpointRequestMapper $requestMapper)
	{
		$this->requestMapper = $requestMapper;
	}

	/**
	 * @return EndpointResponseMapper
	 */
	public function getResponseMapper()
	{
		return $this->responseMapper;
	}

	/**
	 * @param EndpointResponseMapper $responseMapper
	 * @return void
	 */
	public function setResponseMapper(EndpointResponseMapper $responseMapper)
	{
		$this->responseMapper = $responseMapper;
	}

}
