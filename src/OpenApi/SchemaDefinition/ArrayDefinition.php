<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

class ArrayDefinition implements IDefinition
{

	/**
	 * @param mixed[] $data
	 */
	public function __construct(
		private readonly array $data,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function load(): array
	{
		return $this->data;
	}

}
