<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface ILoader
{

	public function load(SchemaBuilder $builder): SchemaBuilder;

}
