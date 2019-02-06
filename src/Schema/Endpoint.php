<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\Utils\Arrays;

final class Endpoint
{

	// Methods
	public const METHOD_GET = 'GET';
	public const METHOD_POST = 'POST';
	public const METHOD_PUT = 'PUT';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_OPTIONS = 'OPTIONS';
	public const METHOD_PATCH = 'PATCH';

	public const METHODS = [
		self::METHOD_GET,
		self::METHOD_POST,
		self::METHOD_PUT,
		self::METHOD_DELETE,
		self::METHOD_OPTIONS,
		self::METHOD_PATCH,
	];

	// Tags
	public const TAG_ID = 'id';

	/** @var string[] */
	private $methods = [];

	/** @var string|null */
	private $mask;

	/** @var string|null */
	private $pattern;

	/** @var EndpointHandler */
	private $handler;

	/** @var string|null */
	private $description;

	/** @var EndpointParameter[] */
	private $parameters = [];

	/** @var EndpointRequest|null */
	private $request;

	/** @var EndpointResponse[] */
	private $responses = [];

	/** @var EndpointNegotiation[] */
	private $negotiations = [];

	/** @var EndpointRequestMapper|null */
	private $requestMapper;

	/** @var EndpointResponseMapper|null */
	private $responseMapper;

	/** @var mixed[] */
	private $tags = [];

	/** @var mixed[] */
	private $metadata = [];

	/** @var mixed[] */
	private $openApi = [];

	public function __construct(EndpointHandler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * @return string[]
	 */
	public function getMethods(): array
	{
		return $this->methods;
	}

	/**
	 * @param string[] $methods
	 */
	public function setMethods(array $methods): void
	{
		foreach ($methods as $method) {
			$this->addMethod($method);
		}
	}

	public function addMethod(string $method): void
	{
		$method = strtoupper($method);

		if (!in_array($method, self::METHODS, true)) {
			throw new InvalidArgumentException(sprintf('Method %s is not allowed', $method));
		}

		$this->methods[] = $method;
	}

	public function hasMethod(string $method): bool
	{
		return in_array(strtoupper($method), $this->methods, true);
	}

	public function getMask(): ?string
	{
		return $this->mask;
	}

	public function setMask(?string $mask): void
	{
		$this->mask = $mask;
	}

	public function getPattern(): string
	{
		if (!$this->pattern) {
			$this->pattern = $this->generatePattern();
		}

		return $this->pattern;
	}

	public function setPattern(?string $pattern): void
	{
		$this->pattern = $pattern;
	}

	public function getHandler(): EndpointHandler
	{
		return $this->handler;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return EndpointParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	/**
	 * @return EndpointParameter[]
	 */
	public function getParametersByIn(string $in): array
	{
		return array_filter($this->getParameters(), function (EndpointParameter $parameter) use ($in) {
			return $parameter->getIn() === $in;
		});
	}

	public function hasParameter(string $name): bool
	{
		return isset($this->parameters[$name]);
	}

	public function addParameter(EndpointParameter $param): void
	{
		$this->parameters[$param->getName()] = $param;
	}

	/**
	 * @param EndpointParameter[] $parameters
	 */
	public function setParameters(array $parameters): void
	{
		foreach ($parameters as $param) {
			$this->addParameter($param);
		}
	}

	/**
	 * @return EndpointResponse[]
	 */
	public function getResponses(): array
	{
		return $this->responses;
	}

	public function hasResponse(string $code): bool
	{
		return isset($this->responses[$code]);
	}

	public function addResponse(EndpointResponse $response): void
	{
		$this->responses[$response->getCode()] = $response;
	}

	/**
	 * @param EndpointResponse[] $responses
	 */
	public function setResponses(array $responses): void
	{
		foreach ($responses as $response) {
			$this->addResponse($response);
		}
	}

	public function setRequest(?EndpointRequest $request): void
	{
		$this->request = $request;
	}

	public function getRequest(): ?EndpointRequest
	{
		return $this->request;
	}

	/**
	 * @return mixed[]
	 */
	public function getOpenApi(): array
	{
		return $this->openApi;
	}

	/**
	 * @param mixed[] $openApi
	 */
	public function setOpenApi(array $openApi): void
	{
		$this->openApi = $openApi;
	}

	/**
	 * @return EndpointNegotiation[]
	 */
	public function getNegotiations(): array
	{
		return $this->negotiations;
	}

	public function addNegotiation(EndpointNegotiation $negotiation): void
	{
		$this->negotiations[] = $negotiation;
	}

	/**
	 * @param EndpointNegotiation[] $negotiations
	 */
	public function setNegotiations(array $negotiations): void
	{
		$this->negotiations = $negotiations;
	}

	/**
	 * @return mixed[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	/**
	 * @return mixed
	 */
	public function getTag(string $name)
	{
		return $this->tags[$name] ?? null;
	}

	public function hasTag(string $name): bool
	{
		return array_key_exists($name, $this->tags);
	}

	/**
	 * @param mixed $value
	 */
	public function addTag(string $name, $value = null): void
	{
		$this->tags[$name] = $value;
	}

	/**
	 * @param mixed $value
	 */
	public function setAttribute(string $key, $value): void
	{
		$this->metadata[$key] = $value;
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getAttribute(string $key, $default = null)
	{
		return Arrays::get($this->metadata, $key, $default);
	}

	private function generatePattern(): string
	{
		$rawPattern = $this->getAttribute('pattern');

		if ($rawPattern === null) {
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
			return sprintf(
				'#%s' . // Always start with raw pattern
				'(?:%s)?$#', // Optionally contains dot followed by one of suffixes
				$rawPattern,
				implode('|', array_map('preg_quote', $suffixes))
			);
		}

		return sprintf(
			'#%s$#', // Exactly match raw pattern
			$rawPattern
		);
	}

	public function getRequestMapper(): ?EndpointRequestMapper
	{
		return $this->requestMapper;
	}

	public function setRequestMapper(?EndpointRequestMapper $requestMapper): void
	{
		$this->requestMapper = $requestMapper;
	}

	public function getResponseMapper(): ?EndpointResponseMapper
	{
		return $this->responseMapper;
	}

	public function setResponseMapper(?EndpointResponseMapper $responseMapper): void
	{
		$this->responseMapper = $responseMapper;
	}

}
