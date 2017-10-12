<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class GroupPathValidation implements IValidation
{

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder)
	{
		$this->validateSlashes($builder);
		$this->validateRegex($builder);
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

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateRegex(SchemaBuilder $builder)
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			$paths = $controller->getGroupPaths();

			foreach ($paths as $path) {
				// Allowed characters:
				// -> a-z
				// -> A-Z
				// -> 0-9
				// -> -_/
				$match = Regex::match($path, '#([^a-zA-Z0-9\-_/]+)#');

				if ($match !== NULL) {
					throw new InvalidSchemaException(
						sprintf(
							'@GroupPath "%s" in "%s" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/].',
							$path,
							$controller->getClass(),
							$match[1]
						)
					);
				}
			}
		}
	}

}
