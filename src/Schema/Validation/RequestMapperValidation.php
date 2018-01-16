<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;

class RequestMapperValidation implements IValidation
{

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder)
	{
		$this->validateEntityClassname($builder);
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateEntityClassname(SchemaBuilder $builder)
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {

				// Skip if @RequestMapper is not set
				if (empty($method->getRequestMapper())) continue;

				$mapper = $method->getRequestMapper();

				if (!class_exists($mapper['entity'], TRUE)) {
					throw new InvalidSchemaException(
						sprintf(
							'Entity "%s" in "%s::%s()" does not exist"',
							$mapper['entity'],
							$controller->getClass(),
							$method->getName()
						)
					);
				}
			}
		}
	}

}
