<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;

class RequestMapperValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateEntityClassname($builder);
	}

	protected function validateEntityClassname(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {

				// Skip if @RequestMapper is not set
				if (empty($method->getRequestMapper())) continue;

				$mapper = $method->getRequestMapper();

				if (!class_exists($mapper['entity'], true)) {
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
