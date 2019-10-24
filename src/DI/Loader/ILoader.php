<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\SchemaBuilder;

interface ILoader
{

	public function load(SchemaBuilder $builder): SchemaBuilder;

}
