<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Schema\Builder\SchemaBuilder;

interface IValidation
{

	public function validate(SchemaBuilder $builder): void;

}
