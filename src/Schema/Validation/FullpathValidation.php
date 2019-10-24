<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Validation;

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Utils\Helpers;

class FullpathValidation implements IValidation
{

	public function validate(SchemaBuilder $builder): void
	{
		$this->validateDuplicities($builder);
	}

	protected function validateDuplicities(SchemaBuilder $builder): void
	{
		$controllers = $builder->getControllers();

		// Init paths
		$paths = [
			Endpoint::METHOD_GET => [],
			Endpoint::METHOD_POST => [],
			Endpoint::METHOD_PUT => [],
			Endpoint::METHOD_DELETE => [],
			Endpoint::METHOD_OPTIONS => [],
			Endpoint::METHOD_PATCH => [],
		];

		foreach ($controllers as $controller) {
			foreach ($controller->getMethods() as $method) {
				foreach ($method->getHttpMethods() as $httpMethod) {

					$maskp = array_merge(
						$controller->getGroupPaths(),
						[$controller->getPath()],
						[$method->getPath()]
					);
					$mask = implode('/', $maskp);
					$mask = Helpers::slashless($mask);
					$mask = '/' . trim($mask, '/');

					if (array_key_exists($mask, $paths[$httpMethod])) {
						throw new InvalidSchemaException(
							sprintf(
								'Duplicate path "%s" in "%s()" and "%s()"',
								$mask,
								$controller->getClass() . '::' . $method->getName(),
								$paths[$httpMethod][$mask]['controller']->getClass() . '::' . $paths[$httpMethod][$mask]['method']->getName()
							)
						);
					}

					$paths[$httpMethod][$mask] = [
						'controller' => $controller,
						'method' => $method,
					];
				}
			}
		}
	}

}
