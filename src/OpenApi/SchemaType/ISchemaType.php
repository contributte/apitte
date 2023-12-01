<?php declare(strict_types = 1);

namespace Apitte\OpenApi\SchemaType;

use Apitte\Core\Schema\EndpointParameter;
use Contributte\OpenApi\Schema\Schema;

interface ISchemaType
{

	public function createSchema(EndpointParameter $endpointParameter): Schema;

}
