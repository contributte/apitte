<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;

class RequestValidation implements IValidation
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

				$request = $method->getRequest();

				if ($request === null) {
					continue;
				}

				$entity = $request->getEntity();

				if ($entity === null) {
					continue;
				}

				if (!class_exists($entity, true)) {
					throw new InvalidSchemaException(
						sprintf(
							'Request entity "%s" in "%s::%s()" does not exist"',
							$request->getEntity(),
							$controller->getClass(),
							$method->getName()
						)
					);
				}
			}
		}
	}

}
