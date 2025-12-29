<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class ControllerPathValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateSlashes($builder);
		$this->validateRegex($builder);
	}

	protected function validateSlashes(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			$path = $controller->getPath();

			if ($path === '') {
				throw new InvalidSchemaException(
					sprintf('#[Path] in "%s" must be set.', $controller->getClass())
				);
			}

			if ($path === '/') {
				continue;
			}

			// MUST: Starts with slash (/)
			if (!str_starts_with($path, '/')) {
				throw new InvalidSchemaException(
					sprintf('#[Path] "%s" in "%s" must starts with "/" (slash).', $path, $controller->getClass())
				);
			}

			// MUST NOT: Ends with slash (/)
			if (str_ends_with($path, '/')) {
				throw new InvalidSchemaException(
					sprintf('#[Path] "%s" in "%s" must not ends with "/" (slash).', $path, $controller->getClass())
				);
			}
		}
	}

	protected function validateRegex(SchemaBuilder $builder): void
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

			if ($match !== null) {
				throw new InvalidSchemaException(
					sprintf(
						'#[Path] "%s" in "%s" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/].',
						$path,
						$controller->getClass(),
						$match[1]
					)
				);
			}
		}
	}

}
