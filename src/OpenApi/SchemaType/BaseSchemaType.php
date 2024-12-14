<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaType;

use Apitte\Core\Schema\EndpointParameter;
use Contributte\OpenApi\Schema\Schema;

class BaseSchemaType implements ISchemaType
{

	public function createSchema(EndpointParameter $endpointParameter): Schema
	{
		switch ($endpointParameter->getType()) {
			case EndpointParameter::TYPE_STRING:
				return new Schema(
					[
						'type' => 'string',
					]
				);
			case EndpointParameter::TYPE_INTEGER:
				return new Schema(
					[
						'type' => 'integer',
						'format' => 'int32',
					]
				);
			case EndpointParameter::TYPE_FLOAT:
				return new Schema(
					[
						'type' => 'float',
						'format' => 'float64',
					]
				);
			case EndpointParameter::TYPE_BOOLEAN:
				return new Schema(
					[
						'type' => 'boolean',
					]
				);
			case EndpointParameter::TYPE_DATETIME:
				return new Schema(
					[
						'type' => 'string',
						'format' => 'date-time',
					]
				);
			case EndpointParameter::TYPE_ENUM:
				return new Schema(
					[
						'type' => 'string',
					]
				);
			default:
				throw new UnknownSchemaType('Unknown endpoint parameter type ' . $endpointParameter->getType());
		}
	}

}
