<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Operation
{

	/** @var string[] */
	private array $tags = [];

	/** @var string|null */
	private ?string $summary = null;

	/** @var string|null */
	private ?string $description = null;

	/** @var ExternalDocumentation|null */
	private ?ExternalDocumentation $externalDocs = null;

	/** @var string|null */
	private ?string $operationId = null;

	/** @var Parameter[]|Reference[] */
	private array $parameters = [];

	/** @var RequestBody|Reference|null */
	private $requestBody = null;

	/** @var Responses */
	private Responses $responses;

	/** @var Callback[]|Reference[] */
	private array $callbacks = [];

	/** @var bool */
	private bool $deprecated = false;

	/** @var SecurityRequirement[] */
	private array $security = [];

	/** @var Server[] */
	private array $servers = [];

	public function __construct(Responses $responses)
	{
		$this->responses = $responses;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Operation
	{
		$responses = Responses::fromArray($data['responses']);
		$operation = new Operation($responses);
		if (isset($data['deprecated'])) {
			$operation->setDeprecated($data['deprecated']);
		}

		$operation->setOperationId($data['operationId'] ?? null);
		$operation->setTags($data['tags'] ?? []);
		$operation->setSummary($data['summary'] ?? null);
		$operation->setDescription($data['description'] ?? null);
		if (isset($data['externalDocs'])) {
			$operation->setExternalDocs(ExternalDocumentation::fromArray($data['externalDocs']));
		}

		foreach ($data['parameters'] ?? [] as $parameterData) {
			if (isset($parameterData['$ref'])) {
				$operation->addParameter(new Reference($parameterData['$ref']));
				continue;
			}

			$operation->addParameter(Parameter::fromArray($parameterData));
		}

		if (isset($data['requestBody'])) {
			if (isset($data['requestBody']['$ref'])) {
				$operation->setRequestBody(new Reference($data['requestBody']['$ref']));
			} else {
				$operation->setRequestBody(RequestBody::fromArray($data['requestBody']));
			}
		}

		foreach ($data['security'] ?? [] as $securityRequirementData) {
			$operation->addSecurityRequirement(SecurityRequirement::fromArray($securityRequirementData));
		}

		foreach ($data['servers'] ?? [] as $server) {
			$operation->addServer(Server::fromArray($server));
		}

		foreach ($data['callbacks'] ?? [] as $callback) {
			if (isset($callback['$ref'])) {
				$operation->addCallback(new Reference($callback['$ref']));
			} else {
				$operation->addCallback(Callback::fromArray($callback));
			}
		}

		return $operation;
	}

	public function setOperationId(?string $operationId): void
	{
		$this->operationId = $operationId;
	}

	/**
	 * @param string[] $tags
	 */
	public function setTags(array $tags): void
	{
		$this->tags = $tags;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setExternalDocs(?ExternalDocumentation $externalDocs): void
	{
		$this->externalDocs = $externalDocs;
	}

	/**
	 * @param Parameter|Reference $parameter
	 */
	public function addParameter($parameter): void
	{
		$this->parameters[] = $parameter;
	}

	/**
	 * @param RequestBody|Reference|null $requestBody
	 */
	public function setRequestBody($requestBody): void
	{
		$this->requestBody = $requestBody;
	}

	/**
	 * @param Callback|Reference $callback
	 */
	public function addCallback($callback): void
	{
		$this->callbacks[] = $callback;
	}

	public function setDeprecated(bool $deprecated): void
	{
		$this->deprecated = $deprecated;
	}

	public function addSecurityRequirement(SecurityRequirement $securityRequirement): void
	{
		$this->security[] = $securityRequirement;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->deprecated) {
			$data['deprecated'] = $this->deprecated;
		}

		if ($this->tags !== []) {
			$data['tags'] = $this->tags;
		}

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->externalDocs !== null) {
			$data['externalDocs'] = $this->externalDocs->toArray();
		}

		if ($this->operationId !== null) {
			$data['operationId'] = $this->operationId;
		}

		foreach ($this->parameters as $parameter) {
			$data['parameters'][] = $parameter->toArray();
		}

		if ($this->requestBody !== null) {
			$data['requestBody'] = $this->requestBody->toArray();
		}

		foreach ($this->security as $securityRequirement) {
			$data['security'][] = $securityRequirement->toArray();
		}

		$data['responses'] = $this->responses->toArray();
		foreach ($this->servers as $server) {
			$data['servers'][] = $server->toArray();
		}

		foreach ($this->callbacks as $callback) {
			$data['callbacks'][] = $callback->toArray();
		}

		return $data;
	}

	/**
	 * @return string[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getExternalDocs(): ?ExternalDocumentation
	{
		return $this->externalDocs;
	}

	public function getOperationId(): ?string
	{
		return $this->operationId;
	}

	/**
	 * @return Parameter[]|Reference[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	/**
	 * @return RequestBody|Reference|null
	 */
	public function getRequestBody()
	{
		return $this->requestBody;
	}

	public function getResponses(): Responses
	{
		return $this->responses;
	}

	/**
	 * @return Reference[]|Callback[]
	 */
	public function getCallbacks(): array
	{
		return $this->callbacks;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	/**
	 * @return SecurityRequirement[]
	 */
	public function getSecurity(): array
	{
		return $this->security;
	}

	/**
	 * @return Server[]
	 */
	public function getServers(): array
	{
		return $this->servers;
	}

}
