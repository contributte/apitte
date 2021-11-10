<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition\Entity;

interface IEntityAdapter
{

	/**
	 * @return mixed[]
	 */
	public function getMetadata(string $type): array;

}
