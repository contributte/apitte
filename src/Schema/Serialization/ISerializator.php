<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface ISerializator
{

	/**
	 * @param SchemaBuilder $builder
	 * @return mixed
	 */
	public function serialize(SchemaBuilder $builder);

}
