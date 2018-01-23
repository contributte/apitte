<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class ControllerPathValidation implements IValidation
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
			$path = $controller->getPath();

			if (strlen($path) === 1) {
				if ($path === '/') continue;

				// MUST: Be exactly /
				throw new InvalidSchemaException(
					sprintf('@ControllerPath "%s" in "%s" must be exactly "/" (slash).', $path, $controller->getClass())
				);
			}

			// MUST: Starts with slash (/)
			if (substr($path, 0, 1) !== '/') {
				throw new InvalidSchemaException(
					sprintf('@ControllerPath "%s" in "%s" must starts with "/" (slash).', $path, $controller->getClass())
				);
			}

			// MUST NOT: Ends with slash (/)
			if (substr($path, -1, 1) === '/') {
				throw new InvalidSchemaException(
					sprintf('@ControllerPath "%s" in "%s" must not ends with "/" (slash).', $path, $controller->getClass())
				);
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
			$path = $controller->getPath();

			// Allowed characters:
			// -> a-z
			// -> A-Z
			// -> 0-9
			// -> -_/
			$match = Regex::match($path, '#([^a-zA-Z0-9\-_/]+)#');

			if ($match !== NULL) {
				throw new InvalidSchemaException(
					sprintf(
						'@ControllerPath "%s" in "%s" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/].',
						$path,
						$controller->getClass(),
						$match[1]
					)
				);
			}
		}
	}

}
