<?php

namespace Apitte\Core\Schema\Builder\Controller;

final class MethodNegotiation
{

	/** @var string */
	private $type;

	/** @var array */
	private $metadata = [];

	/**
	 * Create negotiation
	 */
	public function __construct()
	{
	}

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function addMetadata($key, $value)
	{
		$this->metadata[$key] = $value;
	}

	/**
	 * @param array $metadata
	 * @return void
	 */
	public function setMetadata(array $metadata)
	{
		$this->metadata = $metadata;
	}

}
