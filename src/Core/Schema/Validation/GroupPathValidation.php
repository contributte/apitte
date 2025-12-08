<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class GroupPathValidation implements IValidation
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
			foreach ($controller->getGroupPaths() as $groupPath) {
				if ($groupPath === '/') {
					// INVALID: nonsense
					throw new InvalidSchemaException(
						sprintf('@Path "%s" in "%s" cannot be only "/", it is nonsense.', $groupPath, $controller->getClass())
					);
				}

				// MUST: Starts with slash (/)
				if (!str_starts_with($groupPath, '/')) {
					throw new InvalidSchemaException(
						sprintf('@Path "%s" in "%s" must starts with "/" (slash).', $groupPath, $controller->getClass())
					);
				}

				// MUST NOT: Ends with slash (/)
				if (str_ends_with($groupPath, '/')) {
					throw new InvalidSchemaException(
						sprintf('@Path "%s" in "%s" must not ends with "/" (slash).', $groupPath, $controller->getClass())
					);
				}
			}
		}
	}

	protected function validateRegex(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			$paths = $controller->getGroupPaths();

			foreach ($paths as $path) {
				// Allowed characters:
				// -> a-z
				// -> A-Z
				// -> 0-9
				// -> -_/{}
				$match = Regex::match($path, '#([^a-zA-Z0-9\-_/{}]+)#');

				if ($match !== null) {
					throw new InvalidSchemaException(
						sprintf(
							'@Path "%s" in "%s" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/{}].',
							$path,
							$controller->getClass(),
							$match[1]
						)
					);
				}

				// Allowed parameter characters:
				// -> a-z
				// -> A-Z
				// -> 0-9
				// -> -_
				// @regex https://regex101.com/r/APckUJ/3
				$matches = Regex::matchAll($path, '#\{(.+)\}#U');

				if ($matches !== null) {
					foreach ($matches as $item) {
						$match = Regex::match($item[1], '#.*([^a-zA-Z0-9\-_]+).*#');

						if ($match !== null) {
							throw (new InvalidSchemaException(
								sprintf(
									'@Path "%s" in "%s" contains illegal characters "%s" in parameter. Allowed characters in parameter are only {[a-z-A-Z0-9-_]+}',
									$path,
									$controller->getClass(),
									$match[1]
								)
							))
								->withController($controller);
						}
					}
				}
			}
		}
	}

}
