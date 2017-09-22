<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface ILoader
{

	/**
	 * @param SchemaBuilder $builder
	 * @return SchemaBuilder
	 */
	public function load(SchemaBuilder $builder);

}
