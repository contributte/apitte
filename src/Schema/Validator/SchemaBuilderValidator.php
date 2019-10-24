<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validator;

use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\IValidation;

class SchemaBuilderValidator
{

	/** @var IValidation[] */
	private $validators = [];

	public function add(IValidation $validator): void
	{
		$this->validators[] = $validator;
	}

	public function validate(SchemaBuilder $builder): void
	{
		foreach ($this->validators as $validator) {
			$validator->validate($builder);
		}
	}

}
