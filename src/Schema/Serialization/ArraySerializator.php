<?php

namespace Apitte\Core\Schema\Serialization;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;
use Apitte\Core\Schema\Builder\Controller\MethodParameter;
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
		$endpoint = $this->serializeInit($controller, $method);
		$this->serializeNegotiations($endpoint, $method);
		$this->serializePattern($endpoint, $method);
		$this->serializeMappers($endpoint, $method);

		return $endpoint;
	}

	/**
	 * @param Controller $controller
	 * @param Method $method
	 * @return array
	 */
	protected function serializeInit(Controller $controller, Method $method)
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

		return $endpoint;
	}

	/**
	 * @param array $endpoint
	 * @param Method $method
	 * @return void
	 */
	protected function serializePattern(array &$endpoint, Method $method)
	{
		$mask = $endpoint['mask'];
		$maskParameters = [];

		/** @var MethodParameter[] $pathParameters */
		$pathParameters = array_filter($method->getParameters(), function (MethodParameter $parameter) {
			return $parameter->getIn() === EndpointParameter::IN_PATH;
		});

		/** @var MethodParameter[] $notPathParameters */
		$notPathParameters = array_filter($method->getParameters(), function (MethodParameter $parameter) {
			return $parameter->getIn() !== EndpointParameter::IN_PATH;
		});

		// Collect variable parameters from URL
		$pattern = Regex::replaceCallback($mask, '#({([a-zA-Z0-9\-_]+)})#U', function ($matches) use (&$endpoint, &$maskParameters, $method) {
			list($whole, $variable, $variableName) = $matches;

			// Duplication check
			if (isset($maskParameters[$variableName])) {
				throw new InvalidStateException(sprintf(sprintf('Duplicate mask parameter "%s" in path "%s"', $variableName, $endpoint['mask'])));
			}

			// Build parameter pattern
			$pattern = sprintf('(?P<%s>[^/]+)', $variableName);

			// Build mask parameters
			$maskParameters[$variableName] = [
				'name' => $variableName,
				'pattern' => $pattern,
			];

			// Returned pattern replace {variable} in mask
			return $pattern;
		});

		// Integrity check for number of defined parameters in annotations
		// and number fo parameters in mask
		if (count($pathParameters) > count($maskParameters)) {
			throw new InvalidStateException(sprintf(
				'Number of @RequestParameters (%d) is bigger then mask parameters (%d)',
				count($pathParameters),
				count($maskParameters)
			));
		}

		// Fulfill endpoint parameters (in path)
		foreach ($maskParameters as $maskParameter) {
			$maskParameter['in'] = EndpointParameter::IN_PATH;
			$this->serializeEndpointParameter($endpoint, $maskParameter, $method);
		}

		// Append all other parameters
		foreach ($notPathParameters as $notPathParameter) {
			$this->serializeEndpointParameter($endpoint, [
				'name' => $notPathParameter->getName(),
				'in' => $notPathParameter->getIn(),
			], $method);
		}

		// Build final regex pattern
		$endpoint['attributes']['pattern'] = $pattern;
	}

	/**
	 * @param array $endpoint
	 * @param Method $method
	 * @return void
	 */
	protected function serializeNegotiations(array &$endpoint, Method $method)
	{
		// Add negotiations
		foreach ($method->getNegotiations() as $negotiation) {
			$endpoint['negotiations'][] = [
				'suffix' => $negotiation->getSuffix(),
				'default' => $negotiation->isDefault(),
				'renderer' => $negotiation->getRenderer(),
			];
		}
	}

	/**
	 * @param array $endpoint
	 * @param Method $method
	 * @return void
	 */
	protected function serializeMappers(array &$endpoint, Method $method)
	{
		// Add request & response mappers
		if ($method->getRequestMapper()) {
			$endpoint['requestMapper'] = $method->getRequestMapper();
		}
		if ($method->getResponseMapper()) {
			$endpoint['responseMapper'] = $method->getResponseMapper();
		}
	}

	/**
	 * @param array $endpoint
	 * @param array $parameter
	 * @param Method $method
	 * @return void
	 */
	protected function serializeEndpointParameter(&$endpoint, array $parameter, Method $method)
	{
		// Build parameters
		$p = [
			'name' => $parameter['name'],
			'type' => EndpointParameter::TYPE_SCALAR,
			'description' => NULL,
			'in' => $parameter['in'],
			'required' => TRUE,
			'allowEmpty' => FALSE,
			'deprecated' => FALSE,
		];

		// Update endpoint parameter by defined annotation
		if ($method->hasParameter($parameter['name'])) {
			$param = $method->getParameters()[$parameter['name']];
			$p['type'] = $param->getType();
			$p['description'] = $param->getDescription();
			$p['in'] = $param->getIn();
			$p['required'] = $param->isRequired();
			$p['allowEmpty'] = $param->isAllowEmpty();
			$p['deprecated'] = $param->isDeprecated();
		}

		// Update endpoint
		$endpoint['parameters'][$parameter['name']] = $p;
	}

}
