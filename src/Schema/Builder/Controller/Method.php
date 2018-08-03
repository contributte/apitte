<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

final class Method
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $path;

	/** @var string|null */
	private $id;

	/** @var string|null */
	private $description;

	/** @var string[] */
	private $methods = [];

	/** @var string[] */
	private $tags = [];

	/** @var string[] */
	private $arguments = [];

	/** @var MethodParameter[] */
	private $parameters = [];

	/** @var MethodNegotiation[] */
	private $negotiations = [];

	/** @var mixed[] */
	private $requestMapper = [];

	/** @var mixed[] */
	private $responseMapper = [];

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPath(): ?string
	{
		return $this->path;
	}

	public function setPath(?string $path): void
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
	 * @return string[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	public function addTag(string $tag): void
	{
		$this->tags[] = $tag;
	}

	/**
	 * @param string[] $tags
	 */
	public function addTags(array $tags): void
	{
		foreach ($tags as $tag) {
			$this->addTag($tag);
		}
	}

	public function addArgument(string $name, string $type): void
	{
		$this->arguments[$name] = $type;
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function addArguments(array $arguments): void
	{
		foreach ($arguments as $type => $name) {
			$this->addArgument($type, $name);
		}
	}

	/**
	 * @return string[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @internal
	 */
	public function addParameter(string $name): MethodParameter
	{
		$parameter = new MethodParameter($name);
		$this->parameters[$name] = $parameter;

		return $parameter;
	}

	public function hasParameter(string $name): bool
	{
		return isset($this->parameters[$name]);
	}

	/**
	 * @return MethodParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function addNegotiation(): MethodNegotiation
	{
		$negotiation = new MethodNegotiation();
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

	/**
	 * @return mixed[]
	 */
	public function getRequestMapper(): array
	{
		return $this->requestMapper;
	}

	/**
	 * @param mixed[] $requestMapper
	 */
	public function setRequestMapper(array $requestMapper): void
	{
		$this->requestMapper = $requestMapper;
	}

	/**
	 * @return mixed[]
	 */
	public function getResponseMapper(): array
	{
		return $this->responseMapper;
	}

	/**
	 * @param mixed[] $responseMapper
	 */
	public function setResponseMapper(array $responseMapper): void
	{
		$this->responseMapper = $responseMapper;
	}

}
