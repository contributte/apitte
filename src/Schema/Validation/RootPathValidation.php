<?php

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Utils\Regex;

class RootPathValidation implements IValidation
{

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	public function validate(SchemaBuilder $builder)
	{
		$this->validateDuplicities($builder);
		$this->validateSlashes($builder);
		$this->validateRegex($builder);
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateDuplicities(SchemaBuilder $builder)
	{
		$rootPaths = [];
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			// If this RootPath exists, throw an exception
			if (array_key_exists($controller->getRootPath(), $rootPaths)) {
				throw new InvalidSchemaException(
					sprintf('Duplicate @RootPath in %s and %s', $controller->getClass(), $rootPaths[$controller->getRootPath()])
				);
			}

			$rootPaths[$controller->getRootPath()] = $controller->getClass();
		}
	}

	/**
	 * @param SchemaBuilder $builder
	 * @return void
	 */
	protected function validateSlashes(SchemaBuilder $builder)
	{
		$controllers = $builder->getControllers();

		foreach ($controllers as $controller) {
			$rootPath = $controller->getRootPath();

			if (strlen($rootPath) === 1) {
				if ($rootPath === '/') continue;

				// MUST: Be exactly /
				throw new InvalidSchemaException(
					sprintf('@RootPath "%s" in "%s" must be exactly "/" (slash).', $rootPath, $controller->getClass())
				);
			}

			// MUST: Starts with slash (/)
			if (substr($rootPath, 0, 1) !== '/') {
				throw new InvalidSchemaException(
					sprintf('@RootPath "%s" in "%s" must starts with "/" (slash).', $rootPath, $controller->getClass())
				);
			}

			// MUST NOT: Ends with slash (/)
			if (substr($rootPath, -1, 1) === '/') {
				throw new InvalidSchemaException(
					sprintf('@RootPath "%s" in "%s" must not ends with "/" (slash).', $rootPath, $controller->getClass())
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
			$rootPath = $controller->getRootPath();

			// Allowed characters:
			// -> a-z
			// -> A-Z
			// -> 0-9
			// -> -_/
			$match = Regex::match($rootPath, '#([^a-zA-Z0-9\-_/]+)#');

			if ($match !== NULL) {
				throw new InvalidSchemaException(
					sprintf(
						'@RootPath "%s" in "%s" contains illegal characters "%s". Allowed characters are only [a-zA-Z0-9-_/].',
						$rootPath,
						$controller->getClass(),
						$match[1]
					)
				);
			}
		}
	}

}
