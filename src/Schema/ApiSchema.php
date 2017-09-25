<?php

namespace Apitte\Core\Schema;

final class ApiSchema
{

	/** @var Endpoint[] */
	private $endpoints = [];

	/** @var array */
	private $cache = [];

	/**
	 * @param Endpoint $endpoint
	 * @return void
	 */
	public function addEndpoint(Endpoint $endpoint)
	{
		$this->endpoints[] = $endpoint;
	}

	/**
	 * @return Endpoint[]
	 */
	public function getEndpoints()
	{
		return $this->endpoints;
	}

	/**
	 * @param string $group
	 * @return Endpoint[]
	 */
	public function getEndpointByGroup($group)
	{
		return $this->getEndpointsByTag(Endpoint::TAG_GROUP, $group);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return Endpoint[]
	 */
	public function getEndpointsByTag($name, $value = NULL)
	{
		$key = rtrim(sprintf('%s/%s', $name, $value), '/');

		if (!isset($this->cache[$key])) {
			$endpoints = [];
			foreach ($this->endpoints as $endpoint) {
				// Skip if endpoint does not have a tag
				if (!$endpoint->hasTag($name)) continue;

				// Skip if value is provided and values are not matched
				if ($value !== NULL && $endpoint->getTag($name) !== $value) continue;

				$endpoints[] = $endpoint;
			}

			$this->cache[$key] = $endpoints;
		}

		return $this->cache[$key];
	}

}
