<?php

namespace Apitte\Core\Schema;

class SchemaInspector
{

	/** @var Schema */
	private $schema;

	/** @var array */
	private $cache = [];

	/**
	 * @param Schema $schema
	 */
	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	/**
	 * @param string $group
	 * @return Endpoint[]
	 */
	public function getEndpointByGroup($group)
	{
		return $this->getEndpointsByTag(Endpoint::TAG_GROUP_IDS, $group);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return Endpoint[]
	 */
	public function getEndpointsByTag($name, $value = NULL)
	{
		$key = rtrim(sprintf('%s/%s', $name, $value), '/');
		$endpoints = $this->schema->getEndpoints();

		if (!isset($this->cache[$key])) {
			$items = [];
			foreach ($endpoints as $endpoint) {
				// Skip if endpoint does not have a tag
				if (!$endpoint->hasTag($name)) continue;

				// Early skip (cause value is NULL => optional)
				if ($value === NULL) {
					$items[] = $endpoint;
					continue;
				}

				// Skip if value is provided and values are not matched
				$tagval = $endpoint->getTag($name);

				// If tagval is string, try to compare strings
				// If tagval is array, try to find it by value
				if (is_string($tagval) && $tagval !== $value) {
					continue;
				} else if (is_array($tagval) && !in_array($value, $tagval)) {
					continue;
				}

				$items[] = $endpoint;
			}

			$this->cache[$key] = $items;
		}

		return $this->cache[$key];
	}

}
