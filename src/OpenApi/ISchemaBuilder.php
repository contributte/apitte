<?php declare(strict_types = 1);

namespace Apitte\OpenApi;

use Apitte\OpenApi\Schema\OpenApi;

interface ISchemaBuilder
{

	public function build(): OpenApi;

}
