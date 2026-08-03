<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaType;

use Apitte\Core\Schema\EndpointParameter;
use Contributte\OpenApi\Schema\Schema;

class BaseSchemaType implements ISchemaType
{

	public function createSchema(EndpointParameter $endpointParameter): Schema
	{
		return match ($endpointParameter->getType()) {
			EndpointParameter::TYPE_STRING,
			EndpointParameter::TYPE_ENUM => new Schema(
				[
					'type' => 'string',
				]
			),
			EndpointParameter::TYPE_INTEGER => new Schema(
				[
					'type' => 'integer',
				]
			),
			EndpointParameter::TYPE_FLOAT => new Schema(
				[
					'type' => 'number',
				]
			),
			EndpointParameter::TYPE_BOOLEAN => new Schema(
				[
					'type' => 'boolean',
				]
			),
			EndpointParameter::TYPE_DATETIME => new Schema(
				[
					'type' => 'string',
					'format' => 'date-time',
				]
			),
			// Custom parameter types (see CoreMappingPlugin) have no mapping of their own.
			// Implement ISchemaType to describe them differently.
			default => new Schema(
				[
					'type' => 'string',
				]
			),
		};
	}

}
