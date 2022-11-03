<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

class SchemaInspector
{

	private Schema $schema;

	/** @var Endpoint[][] */
	private array $cache = [];

	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	/**
	 * @return Endpoint[]
	 */
	public function getEndpointsByTag(string $name, ?string $value = null): array
	{
		$key = rtrim(sprintf('%s/%s', $name, (string) $value), '/');
		$endpoints = $this->schema->getEndpoints();

		if (!isset($this->cache[$key])) {
			$items = [];
			foreach ($endpoints as $endpoint) {
				// Skip if endpoint does not have a tag
				if (!$endpoint->hasTag($name)) {
					continue;
				}

				// Early skip (cause value is null => optional)
				if ($value === null) {
					$items[] = $endpoint;
					continue;
				}

				// Skip if value is provided and values are not matched
				$tagval = $endpoint->getTag($name);

				// If tagval is string, try to compare strings
				if (is_string($tagval) && $tagval !== $value) {
					continue;
				}

				// If tagval is array, try to find it by value
				if (is_array($tagval) && !in_array($value, $tagval, true)) {
					continue;
				}

				$items[] = $endpoint;
			}

			$this->cache[$key] = $items;
		}

		return $this->cache[$key];
	}

}
