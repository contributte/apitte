<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\UI\Controller\IController;

class ControllerValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateInterface($builder);
	}

	protected function validateInterface(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			$class = $controller->getClass();

			if (!is_subclass_of($class, IController::class)) {
				throw new InvalidSchemaException(sprintf('Controller "%s" must implement "%s"', $class, IController::class));
			}
		}
	}

}
