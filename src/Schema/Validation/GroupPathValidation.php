<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;

class GroupPathValidation implements IValidation
{

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder)
	{
		$this->validateSlashes($builder);
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateSlashes(SchemaBuilder $builder)
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getGroupPaths() as $groupPath) {
				if ($groupPath === '/') {
					// INVALID: nonsense
					throw new InvalidSchemaException(
						sprintf('@GroupPath "%s" in "%s" cannot be only "/", it is nonsense.', $groupPath, $controller->getClass())
					);
				}

				// MUST: Starts with slash (/)
				if (substr($groupPath, 0, 1) !== '/') {
					throw new InvalidSchemaException(
						sprintf('@GroupPath "%s" in "%s" must starts with "/" (slash).', $groupPath, $controller->getClass())
					);
				}

				// MUST NOT: Ends with slash (/)
				if (substr($groupPath, -1, 1) === '/') {
					throw new InvalidSchemaException(
						sprintf('@GroupPath "%s" in "%s" must not ends with "/" (slash).', $groupPath, $controller->getClass())
					);
				}
			}
		}
	}

}
