<?php

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface ILoader
{

	/**
	 * @return SchemaBuilder
	 */
	public function load();

}
