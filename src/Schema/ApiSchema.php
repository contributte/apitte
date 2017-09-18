<?php

namespace Apitte\Core\Schema;

final class ApiSchema
{

	/** @var Endpoint[] */
	private $endpoints = [];

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

}
