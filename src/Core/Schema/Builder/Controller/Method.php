<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

use Apitte\Core\Schema\EndpointNegotiation;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\EndpointResponse;

class Method
{

	private string $name;

	private string $path = '';

	private ?string $id = null;

	/** @var string[] */
	private array $httpMethods = [];

	/** @var mixed[] */
	private array $tags = [];

	private ?EndpointRequestBody $requestBody = null;

	/** @var EndpointParameter[] */
	private array $parameters = [];

	/** @var EndpointResponse[] */
	private array $responses = [];

	/** @var EndpointNegotiation[] */
	private array $negotiations = [];

	/** @var mixed[] */
	private array $openApi = [];

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): void
	{
		$this->id = $id;
	}

	/**
	 * @return string[]
	 */
	public function getHttpMethods(): array
	{
		return $this->httpMethods;
	}

	/**
	 * @param string[] $httpMethods
	 */
	public function setHttpMethods(array $httpMethods): void
	{
		$this->httpMethods = $httpMethods;
	}

	public function addHttpMethod(string $method): void
	{
		$this->httpMethods[] = strtoupper($method);
	}

	/**
	 * @param string[] $httpMethods
	 */
	public function addHttpMethods(array $httpMethods): void
	{
		foreach ($httpMethods as $httpMethod) {
			$this->addHttpMethod($httpMethod);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	public function addTag(string $name, mixed $value = null): void
	{
		$this->tags[$name] = $value;
	}

	/**
	 * @param mixed[] $tags
	 */
	public function addTags(array $tags): void
	{
		foreach ($tags as $name => $value) {
			$this->addTag($name, $value);
		}
	}

	public function addParameter(string $name, string $type = EndpointParameter::TYPE_STRING): EndpointParameter
	{
		$parameter = new EndpointParameter($name, $type);
		$this->parameters[$name] = $parameter;

		return $parameter;
	}

	public function getRequestBody(): ?EndpointRequestBody
	{
		return $this->requestBody;
	}

	public function setRequestBody(?EndpointRequestBody $requestBody): void
	{
		$this->requestBody = $requestBody;
	}

	public function addResponse(string $code, string $description): EndpointResponse
	{
		$response = new EndpointResponse($code, $description);
		$this->responses[$code] = $response;

		return $response;
	}

	public function hasParameter(string $name): bool
	{
		return isset($this->parameters[$name]);
	}

	public function hasResponse(string $code): bool
	{
		return isset($this->responses[$code]);
	}

	/**
	 * @return EndpointParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	/**
	 * @return EndpointResponse[]
	 */
	public function getResponses(): array
	{
		return $this->responses;
	}

	/**
	 * @param mixed[] $openApi
	 */
	public function setOpenApi(array $openApi): void
	{
		$this->openApi = $openApi;
	}

	/**
	 * @return mixed[]
	 */
	public function getOpenApi(): array
	{
		return $this->openApi;
	}

	public function addNegotiation(string $suffix): EndpointNegotiation
	{
		$negotiation = new EndpointNegotiation($suffix);
		$this->negotiations[] = $negotiation;

		return $negotiation;
	}

	/**
	 * @return EndpointNegotiation[]
	 */
	public function getNegotiations(): array
	{
		return $this->negotiations;
	}

}
