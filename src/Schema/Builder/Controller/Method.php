<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

use Apitte\Core\Schema\EndpointParameter;

final class Method
{

	/** @var string */
	private $name;

	/** @var string */
	private $path = '';

	/** @var string|null */
	private $id;

	/** @var string|null */
	private $description;

	/** @var string[] */
	private $methods = [];

	/** @var mixed[] */
	private $tags = [];

	/** @var MethodRequest|null */
	private $request;

	/** @var MethodParameter[] */
	private $parameters = [];

	/** @var MethodResponse[] */
	private $responses = [];

	/** @var MethodNegotiation[] */
	private $negotiations = [];

	/** @var RequestMapper|null */
	private $requestMapper;

	/** @var ResponseMapper|null */
	private $responseMapper;

	/** @var mixed[] */
	private $openApi = [];

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

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
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
		$this->methods = $methods;
	}

	public function addMethod(string $method): void
	{
		$this->methods[] = strtoupper($method);
	}

	/**
	 * @param string[] $methods
	 */
	public function addMethods(array $methods): void
	{
		foreach ($methods as $method) {
			$this->addMethod($method);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	/**
	 * @param mixed $value
	 */
	public function addTag(string $name, $value = null): void
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

	public function addParameter(string $name, string $type = EndpointParameter::TYPE_STRING): MethodParameter
	{
		$parameter = new MethodParameter($name, $type);
		$this->parameters[$name] = $parameter;

		return $parameter;
	}

	public function getRequest(): ?MethodRequest
	{
		return $this->request;
	}

	public function setRequest(?MethodRequest $request): void
	{
		$this->request = $request;
	}

	public function addResponse(string $code, string $description): MethodResponse
	{
		$response = new MethodResponse($code, $description);
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
	 * @return MethodParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	/**
	 * @return MethodResponse[]
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

	public function addNegotiation(string $suffix): MethodNegotiation
	{
		$negotiation = new MethodNegotiation($suffix);
		$this->negotiations[] = $negotiation;

		return $negotiation;
	}

	/**
	 * @return MethodNegotiation[]
	 */
	public function getNegotiations(): array
	{
		return $this->negotiations;
	}

	public function getRequestMapper(): ?RequestMapper
	{
		return $this->requestMapper;
	}

	public function setRequestMapper(string $entity, bool $validation = true): void
	{
		$this->requestMapper = new RequestMapper($entity, $validation);
	}

	public function getResponseMapper(): ?ResponseMapper
	{
		return $this->responseMapper;
	}

	public function setResponseMapper(string $entity): void
	{
		$this->responseMapper = new ResponseMapper($entity);
	}

}
