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
					'format' => 'int32',
				]
			),
			EndpointParameter::TYPE_FLOAT => new Schema(
				[
					'type' => 'float',
					'format' => 'float64',
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
			default => throw new UnknownSchemaType('Unknown endpoint parameter type ' . $endpointParameter->getType()),
		};
	}

}
