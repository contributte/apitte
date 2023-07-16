<?php declare(strict_types = 1);

namespace Apitte\OpenApi\Schema;

class Paths
{

	/** @var PathItem[]|Reference[] */
	private array $paths = [];

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Paths
	{
		$paths = new Paths();
		foreach ($data as $path => $pathItemData) {
			if (isset($pathItemData['$ref'])) {
				$paths->setPathItem($path, Reference::fromArray($pathItemData));

				continue;
			}

			$paths->setPathItem($path, PathItem::fromArray($pathItemData));
		}

		return $paths;
	}

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

	public function setPathItem(string $path, PathItem|Reference $pathItem): void
	{
		$this->paths[$path] = $pathItem;
	}

	public function getPath(string $path): PathItem|Reference|null
	{
		return $this->paths[$path] ?? null;
	}

}
