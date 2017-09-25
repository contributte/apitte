<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\SchemaMapping;
use Apitte\Core\Utils\Helpers;
use Apitte\Core\Utils\Regex;

final class ArraySerializator implements ISerializator
{

	/**
	 * @param SchemaBuilder $builder
	 * @return array
	 */
	public function serialize(SchemaBuilder $builder)
	{
		$controllers = $builder->getControllers();
		$schema = [];

		// Iterate over all controllers
		foreach ($controllers as $controller) {

			// Iterate over all controller api methods
			foreach ($controller->getMethods() as $method) {

				// Skip invalid methods
				if (empty($method->getPath())) continue;

				// Build full mask (@RootPath + @Path)
				// without duplicated slashes (//)
				// and without trailing slash at the end
				// but with slash at the beginning
				$mask = '/' . $controller->getRootPath() . '/' . $method->getPath();
				$mask = Helpers::slashless($mask);
				$mask = '/' . trim($mask, '/');

				// Create endpoint
				$endpoint = [
					SchemaMapping::HANDLER => [
						SchemaMapping::HANDLER_CLASS => $controller->getClass(),
						SchemaMapping::HANDLER_METHOD => $method->getName(),
						SchemaMapping::HANDLER_ARGUMENTS => $method->getArguments(),
					],
					SchemaMapping::METHODS => $method->getMethods(),
					SchemaMapping::ROOT_PATH => $controller->getRootPath(),
					SchemaMapping::PATH => $method->getPath(),
					SchemaMapping::MASK => $mask,
					SchemaMapping::PARAMETERS => [],
					SchemaMapping::PATTERN => $mask,
				];

				// Collect variable parameters from URL
				$pattern = Regex::replaceCallback($mask, '#({([a-zA-Z0-9\-_]+)})#U', function ($matches) use (&$endpoint, $method) {
					list($whole, $variable, $variableName) = $matches;

					// Build parameter pattern
					$pattern = sprintf('(?P<%s>[^/]+)', $variableName);

					// Build parameters
					$parameters = [
						SchemaMapping::PARAMETERS_NAME => $variableName,
						SchemaMapping::PARAMETERS_PATTERN => $pattern,
						SchemaMapping::PARAMETERS_TYPE => EndpointParameter::TYPE_SCALAR,
						SchemaMapping::PARAMETERS_DESCRIPTION => NULL,
					];

					// Update endpoint parameters by defined annotation
					if ($method->hasParameter($variableName)) {
						$parameter = $method->getParameters()[$variableName];
						$parameters[SchemaMapping::PARAMETERS_TYPE] = $parameter->getType();
						$parameters[SchemaMapping::PARAMETERS_DESCRIPTION] = $parameter->getDescription();
					}

					// Update endpoint
					$endpoint[SchemaMapping::PARAMETERS][$variableName] = $parameters;

					// Returned pattern replace {variable} in mask
					return $pattern;
				});

				// Build final regex pattern
				$endpoint[SchemaMapping::PATTERN] = sprintf('#%s$/?\z#A', $pattern);

				// Append to schema
				$schema[] = $endpoint;
			}
		}

		return $schema;
	}

}
