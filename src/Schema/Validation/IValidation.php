<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface IValidation
{

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder);

}
