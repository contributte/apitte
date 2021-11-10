<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class PathItem
{

	public const
		OPERATION_GET = 'get',
		OPERATION_PUT = 'put',
		OPERATION_POST = 'post',
		OPERATION_DELETE = 'delete',
		OPERATION_OPTIONS = 'options',
		OPERATION_HEAD = 'head',
		OPERATION_PATCH = 'patch',
		OPERATION_TRACE = 'trace';

	/** @var string[] */
	private static $allowedOperations = [
		self::OPERATION_GET,
		self::OPERATION_PUT,
		self::OPERATION_POST,
		self::OPERATION_DELETE,
		self::OPERATION_OPTIONS,
		self::OPERATION_HEAD,
		self::OPERATION_PATCH,
		self::OPERATION_TRACE,
	];

	/** @var string|null */
	private $ref;

	/** @var string|null */
	private $summary;

	/** @var string|null */
	private $description;

	/** @var Operation[] */
	private $operations = [];

	/** @var Server[] */
	private $servers = [];

	/** @var Parameter|Reference */
	private $params;

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

		return $pathItem;
	}

	public function setOperation(string $key, Operation $operation): void
	{
		if (!in_array($key, self::$allowedOperations, true)) {
			return;
		}

		$this->operations[$key] = $operation;
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

		return $data;
	}

}
