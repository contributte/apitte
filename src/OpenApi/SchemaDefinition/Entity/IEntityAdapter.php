<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaDefinition\Entity;

use Apitte\OpenApi\SchemaDefinition\BaseDefinition;

interface IEntityAdapter
{

	/**
	 * @return mixed[]
	 */
	public function getMetadata(string $type, string $version = BaseDefinition::DEFAULT_VERSION): array;

}
