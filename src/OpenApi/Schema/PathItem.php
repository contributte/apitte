<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class PathItem
{

	public const OPERATION_GET = 'get';
	public const OPERATION_PUT = 'put';
	public const OPERATION_POST = 'post';
	public const OPERATION_DELETE = 'delete';
	public const OPERATION_OPTIONS = 'options';
	public const OPERATION_HEAD = 'head';
	public const OPERATION_PATCH = 'patch';
	public const OPERATION_TRACE = 'trace';

	/** @var string[] */
	private static array $allowedOperations = [
		self::OPERATION_GET,
		self::OPERATION_PUT,
		self::OPERATION_POST,
		self::OPERATION_DELETE,
		self::OPERATION_OPTIONS,
		self::OPERATION_HEAD,
		self::OPERATION_PATCH,
		self::OPERATION_TRACE,
	];

	private ?string $summary = null;

	private ?string $description = null;

	/** @var Operation[] */
	private array $operations = [];

	/** @var Server[] */
	private array $servers = [];

	/** @var Parameter[]|Reference[] */
	private array $params = [];

	/**
	 * @param mixed[] $pathItemData
	 */
	public static function fromArray(array $pathItemData): PathItem
	{
		$pathItem = new PathItem();

		foreach (self::$allowedOperations as $allowedOperation) {
			if (!isset($pathItemData[$allowedOperation])) {
				continue;
			}

			$pathItem->setOperation($allowedOperation, Operation::fromArray($pathItemData[$allowedOperation]));
		}

		$pathItem->setSummary($pathItemData['summary'] ?? null);
		$pathItem->setDescription($pathItemData['description'] ?? null);

		foreach ($pathItemData['servers'] ?? [] as $server) {
			$pathItem->addServer(Server::fromArray($server));
		}

		foreach ($pathItemData['parameters'] ?? [] as $parameter) {
			if (isset($parameter['$ref'])) {
				$pathItem->addParameter(Reference::fromArray($parameter));
			} else {
				$pathItem->addParameter(Parameter::fromArray($parameter));
			}
		}

		return $pathItem;
	}

	public function addParameter(Parameter|Reference $parameter): void
	{
		$this->params[] = $parameter;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setOperation(string $key, Operation $operation): void
	{
		if (!in_array($key, self::$allowedOperations, true)) {
			return;
		}

		$this->operations[$key] = $operation;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return Parameter[]|Reference[]
	 */
	public function getParameters(): array
	{
		return $this->params;
	}

	/**
	 * @return Server[]
	 */
	public function getServers(): array
	{
		return $this->servers;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		foreach ($this->operations as $key => $operation) {
			$data[$key] = $operation->toArray();
		}

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->servers !== []) {
			$data['servers'] = array_map(static fn (Server $server): array => $server->toArray(), $this->servers);
		}

		if ($this->params !== []) {
			$data['parameters'] = array_map(static fn (Parameter|Reference $parameter): array => $parameter->toArray(), $this->params);
		}

		return $data;
	}

}
