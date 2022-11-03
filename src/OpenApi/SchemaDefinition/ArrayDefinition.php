<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

class ArrayDefinition implements IDefinition
{

	/** @var mixed[] */
	private array $data = [];

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		return $this->data;
	}

}
