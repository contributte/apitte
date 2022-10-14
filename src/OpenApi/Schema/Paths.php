<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Paths
{

	/** @var PathItem[] */
	private array $paths = [];

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		foreach ($this->paths as $key => $pathItem) {
			$data[$key] = $pathItem->toArray();
		}

		return $data;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Paths
	{
		$paths = new Paths();
		foreach ($data as $path => $pathItemData) {
			$paths->setPathItem($path, PathItem::fromArray($pathItemData));
		}

		return $paths;
	}

	public function setPathItem(string $path, PathItem $pathItem): void
	{
		$this->paths[$path] = $pathItem;
	}

	public function getPath(string $path): ?PathItem
	{
		return $this->paths[$path] ?? null;
	}

}
