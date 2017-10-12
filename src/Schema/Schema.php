<?php

namespace Apitte\Core\Schema;

class Schema
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
