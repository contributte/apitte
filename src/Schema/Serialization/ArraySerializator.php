<?php declare(strict_types = 1);

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
	 * @return mixed[]
	 */
	public function serialize(SchemaBuilder $builder): array
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
	 * @return mixed[]
	 */
	protected function serializeEndpoint(Controller $controller, Method $method): array
	{
		$endpoint = $this->serializeInit($controller, $method);
		$this->serializeNegotiations($endpoint, $method);
		$this->serializePattern($endpoint, $controller, $method);
		$this->serializeMappers($endpoint, $method);

		return $endpoint;
	}

	/**
	 * @return mixed[]
	 */
	protected function serializeInit(Controller $controller, Method $method): array
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
			$id = null;
		} else {
			$idp = array_merge(
				$controller->getGroupIds(),
				[$controller->getId()],
				[$method->getId()]
			);
			$id = implode('.', $idp);
		}

		// Create endpoint
		return [
			'handler' => [
				'class' => $controller->getClass(),
				'method' => $method->getName(),
				'arguments' => $method->getArguments(),
			],
			'id' => $id,
			'tags' => array_merge($controller->getTags(), $method->getTags()),
			'methods' => $method->getMethods(),
			'mask' => $mask,
			'description' => $method->getDescription(),
			'parameters' => [],
			'negotiations' => [],
			'attributes' => [
				'pattern' => null,
			],
		];
	}

	/**
	 * @param mixed[] $endpoint
	 */
	protected function serializePattern(array &$endpoint, Controller $controller, Method $method): void
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
		$pattern = Regex::replaceCallback($mask, '#({([a-zA-Z0-9\-_]+)})#U', function ($matches) use (&$endpoint, &$maskParameters) {
			[$whole, $variable, $variableName] = $matches;

			// Duplication check
			if (isset($maskParameters[$variableName])) {
				throw new InvalidStateException(sprintf('Duplicate mask parameter "%s" in path "%s"', $variableName, $endpoint['mask']));
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

		// Check if @RequestParameter(in=path) is also defined in mask
		foreach ($pathParameters as $parameter) {
			foreach ($maskParameters as $maskParameter) {
				if ($maskParameter['name'] === $parameter->getName()) {
					continue 2;
				}
			}
			throw new InvalidStateException(sprintf('@RequestParameter(name="%s", in=path) is not defined in mask (in @GroupPath, @ControllerPath or @Path)', $parameter->getName()));
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
	 * @param mixed[] $endpoint
	 */
	protected function serializeNegotiations(array &$endpoint, Method $method): void
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
	 * @param mixed[] $endpoint
	 */
	protected function serializeMappers(array &$endpoint, Method $method): void
	{
		// Add request & response mappers
		if ($method->getRequestMapper()) {
			$endpoint['requestMapper'] = [
				'entity' => $method->getRequestMapper()->getEntity(),
				'validation' => $method->getRequestMapper()->isValidation(),
			];
		}
		if ($method->getResponseMapper()) {
			$endpoint['responseMapper'] = [
				'entity' => $method->getResponseMapper()->getEntity(),
			];
		}
	}

	/**
	 * @param mixed[] $endpoint
	 * @param mixed[] $parameter
	 */
	protected function serializeEndpointParameter(array &$endpoint, array $parameter, Method $method): void
	{
		// Build parameters
		$p = [
			'name' => $parameter['name'],
			'type' => EndpointParameter::TYPE_STRING,
			'description' => null,
			'in' => $parameter['in'],
			'required' => true,
			'allowEmpty' => false,
			'deprecated' => false,
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
