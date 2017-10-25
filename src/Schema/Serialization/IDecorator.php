<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface IDecorator
{

	/**
	 * @param SchemaBuilder $builder
	 * @return SchemaBuilder
	 */
	public function decorate(SchemaBuilder $builder);

}
