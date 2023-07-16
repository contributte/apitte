<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Link
{

	private ?string $operationRef = null;

	private ?string $operationId = null;

	/** @var mixed[] */
	private array $parameters = [];

	private mixed $requestBody = null;

	private ?string $description = null;

	private ?Server $server = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Link
	{
		$link = new Link();
		$link->setOperationRef($data['operationRef'] ?? null);
		$link->setOperationId($data['operationId'] ?? null);
		$link->setParameters($data['parameters'] ?? []);
		$link->setRequestBody($data['requestBody'] ?? null);
		$link->setDescription($data['description'] ?? null);
		$link->setServer(isset($data['server']) ? Server::fromArray($data['server']) : null);

		return $link;
	}

	public function setOperationRef(?string $operationRef): void
	{
		$this->operationRef = $operationRef;
	}

	public function setOperationId(?string $operationId): void
	{
		$this->operationId = $operationId;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function setParameters(array $parameters): void
	{
		$this->parameters = $parameters;
	}

	public function setRequestBody(mixed $requestBody): void
	{
		$this->requestBody = $requestBody;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setServer(?Server $server): void
	{
		$this->server = $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		if ($this->operationRef !== null) {
			$data['operationRef'] = $this->operationRef;
		}

		if ($this->operationId !== null) {
			$data['operationId'] = $this->operationId;
		}

		if ($this->parameters !== []) {
			$data['parameters'] = $this->parameters;
		}

		if ($this->requestBody !== null) {
			$data['requestBody'] = $this->requestBody;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->server !== null) {
			$data['server'] = $this->server->toArray();
		}

		return $data;
	}

}
