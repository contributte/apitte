<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition;

interface IDefinition
{

	/**
	 * @return mixed[]
	 */
	public function load(): array;

}
