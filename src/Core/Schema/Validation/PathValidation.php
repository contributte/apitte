<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class PathValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateRequirements($builder);
		$this->validateSlashes($builder);
		$this->validateRegex($builder);
	}

	protected function validateRequirements(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				if ($method->getPath() === '') {
					throw (new InvalidSchemaException(
						sprintf(
							'"%s::%s()" has empty #[Path].',
							$controller->getClass(),
							$method->getName()
						)
					))
					 ->withController($controller)
					 ->withMethod($method);
				}
			}
		}
	}

	protected function validateSlashes(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				$path = $method->getPath();

				// MUST: Starts with slash (/)
				if (!str_starts_with($path, '/')) {
					throw (new InvalidSchemaException(
						sprintf(
							'#[Path] "%s" in "%s::%s()" must starts with "/" (slash).',
							$path,
							$controller->getClass(),
							$method->getName()
						)
					))
					 ->withController($controller)
					 ->withMethod($method);
				}

				// MUST NOT: Ends with slash (/), except single '/' path
				if (str_ends_with($path, '/') && strlen($path) > 1) {
					throw (new InvalidSchemaException(
						sprintf(
							'#[Path] "%s" in "%s::%s()" must not ends with "/" (slash).',
							$path,
							$controller->getClass(),
							$method->getName()
						)
					))
					 ->withController($controller)
					 ->withMethod($method);
				}
			}
		}
	}

	protected function validateRegex(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				$path = $method->getPath();

				// Allowed characters:
				// -> a-z
				// -> A-Z
				// -> 0-9
				// -> -_/{}
				// @regex https://regex101.com/r/d7f5YI/1
				$match = Regex::match($path, '#([^a-zA-Z0-9\-_\/{}]+)#');

				if ($match !== null) {
					throw (new InvalidSchemaException(
						sprintf(
							'#[Path] "%s" in "%s::%s()" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/{}].',
							$path,
							$controller->getClass(),
							$method->getName(),
							$match[1]
						)
					))
					 ->withController($controller)
					 ->withMethod($method);
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
									'#[Path] "%s" in "%s::%s()" contains illegal characters "%s" in parameter. Allowed characters in parameter are only {[a-z-A-Z0-9-_]+}',
									$path,
									$controller->getClass(),
									$method->getName(),
									$match[1]
								)
							))
								->withController($controller)
								->withMethod($method);
						}
					}
				}
			}
		}
	}

}
