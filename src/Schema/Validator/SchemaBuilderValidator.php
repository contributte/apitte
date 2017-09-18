<?php

namespace Apitte\Core\Schema\Validator;

use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\IValidation;

class SchemaBuilderValidator
{

	/** @var IValidation[] */
	private $validators = [];

	/**
	 * @param IValidation $validator
	 * @return void
	 */
	public function add(IValidation $validator)
	{
		$this->validators[] = $validator;
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder)
	{
		foreach ($this->validators as $validator) {
			$validator->validate($builder);
		}
	}

}
