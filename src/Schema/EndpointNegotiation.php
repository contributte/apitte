<?php

namespace Apitte\Core\Schema;

final class EndpointNegotiation
{

	const TYPE_SUFFIX = 'suffix';

	/** @var string */
	private $type;

	/** @var array */
	private $metadata = [];

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $type
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
	 * @param array $metadata
	 * @return void
	 */
	public function setMetadata(array $metadata)
	{
		$this->metadata = $metadata;
	}

}
