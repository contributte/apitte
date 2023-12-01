<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Hierarchy;

class HierarchicalNode
{

	private string $path;

	/** @var HierarchicalNode[] */
	private array $nodes = [];

	/** @var ControllerMethodPair[] */
	private array $endpoints = [];

	public function __construct(string $path)
	{
		$this->path = $path;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function addNode(string $path): HierarchicalNode
	{
		if (!isset($this->nodes[$path])) {
			$this->nodes[$path] = new HierarchicalNode($path);
		}

		return $this->nodes[$path];
	}

	public function addEndpoint(ControllerMethodPair $endpoint): void
	{
		// Store endpoint under index with GET, POST, PATCH format
		$httpMethods = $endpoint->getMethod()->getHttpMethods();
		sort($httpMethods);
		$index = implode(', ', $httpMethods);

		$this->endpoints[$index] = $endpoint;
	}

	/**
	 * @return ControllerMethodPair[]
	 */
	public function getSortedEndpoints(): array
	{
		// Return endpoints sorted by HTTP method
		ksort($this->endpoints);

		return array_values($this->endpoints);
	}

	/**
	 * @return HierarchicalNode[]
	 */
	public function getSortedNodes(): array
	{
		$staticNodes = [];
		$variableNodes = [];

		// Divide static and variable nodes
		foreach ($this->nodes as $node) {
			$path = $node->getPath();
			if (str_contains($path, '{') && str_contains($path, '}')) {
				$variableNodes[] = $node;
			} else {
				$staticNodes[] = $node;
			}
		}

		// Sort static nodes from A to Z and keep empty path last
		uasort($staticNodes, static function (HierarchicalNode $a, HierarchicalNode $b): int {
			$pathA = $a->getPath();
			$pathB = $b->getPath();

			// Same path, don't flip
			if ($pathA === $pathB) {
				return 0;
			}

			// Path is empty, keep it last
			if ($pathA === '') {
				return 1;
			}

			// Path is empty, keep it last
			if ($pathB === '') {
				return -1;
			}

			return (strcmp($pathA, $pathB) <= -1) ? -1 : 1;
		});

		return array_merge($staticNodes, $variableNodes);
	}

}
