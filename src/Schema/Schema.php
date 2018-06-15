<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

class Schema
{

	/** @var Endpoint[] */
	private $endpoints = [];

	public function addEndpoint(Endpoint $endpoint): void
	{
		$this->endpoints[] = $endpoint;
	}

	/**
	 * @return Endpoint[]
	 */
	public function getEndpoints(): array
	{
		return $this->endpoints;
	}

}
