<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;
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

				$endpoint = $this->serializeEndpoint($controller, $method);
				$schema[] = $endpoint;
			}
		}

		return $schema;
	}

	/**
	 * @param Controller $controller
	 * @param Method $method
	 * @return array
	 */
	protected function serializeEndpoint(Controller $controller, Method $method)
	{
		// Build full mask (@GroupPath(s) + @ControllerPath + @Path)
		// without duplicated slashes (//)
		// and without trailing slash at the end
		// but with slash at the beginning
		$maskp = array_merge(
			$controller->getGroupPaths(),
			[$controller->getPath()],
			[$method->getPath()]
		);
		$mask = implode('/', $maskp);
		$mask = Helpers::slashless($mask);
		$mask = '/' . trim($mask, '/');

		// Build full id (@GroupId(s) + @ControllerId + @Id)
		// If @Id is empty, then fullid is also empty
		if (empty($method->getId())) {
			$id = NULL;
		} else {
			$idp = array_merge(
				$controller->getGroupIds(),
				[$controller->getId()],
				[$method->getId()]
			);
			$id = implode('.', $idp);
		}

		// Create endpoint
		$endpoint = [
			'handler' => [
				'class' => $controller->getClass(),
				'method' => $method->getName(),
				'arguments' => $method->getArguments(),
			],
			'id' => $id,
			'tags' => $controller->getTags(),
			'methods' => $method->getMethods(),
			'mask' => $mask,
			'description' => $method->getDescription(),
			'parameters' => [],
			'negotiations' => [],
			'attributes' => [
				'pattern' => NULL,
			],
		];

		// Collect variable parameters from URL
		$pattern = Regex::replaceCallback($mask, '#({([a-zA-Z0-9\-_]+)})#U', function ($matches) use (&$endpoint, $method) {
			list($whole, $variable, $variableName) = $matches;

			// Build parameters
			$parameter = [
				'name' => $variableName,
				'type' => EndpointParameter::TYPE_SCALAR,
				'description' => NULL,
			];

			// Update endpoint parameters by defined annotation
			if ($method->hasParameter($variableName)) {
				$param = $method->getParameters()[$variableName];
				$parameter['type'] = $param->getType();
				$parameter['description'] = $param->getDescription();
			}

			// Build parameter pattern
			$pattern = sprintf('(?P<%s>[^/]+)', $variableName);
			$parameter['pattern'] = $pattern;

			// Update endpoint
			$endpoint['parameters'][$variableName] = $parameter;

			// Returned pattern replace {variable} in mask
			return $pattern;
		});

		foreach ($method->getNegotiations() as $negotiation) {
			$endpoint['negotiations'][] = [
				'suffix' => $negotiation->getSuffix(),
				'default' => $negotiation->isDefault(),
				'callback' => $negotiation->getCallback(),
			];
		}

		// Build final regex pattern
		$endpoint['attributes']['pattern'] = $pattern;

		return $endpoint;
	}

}
