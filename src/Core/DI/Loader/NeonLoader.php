<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\SchemaBuilder;

class NeonLoader implements ILoader
{

	/** @var mixed[] */
	private array $schema;

	/**
	 * @param mixed[] $schema
	 */
	public function __construct(array $schema)
	{
		$this->schema = $schema;
	}

	public function load(SchemaBuilder $builder): SchemaBuilder
	{
		foreach ($this->schema as $class => $settings) {
			$controller = $builder->addController($class);
			$controller->setId($settings['id'] ?? null);
			$controller->setPath($settings['path'] ?? '');
			$controller->setGroupIds($settings['groupIds'] ?? []);
			$controller->setGroupPaths($settings['groupPaths'] ?? []);
			$controller->addTags($settings['tags'] ?? []);
			$controller->setOpenApi($settings['openapi'] ?? []);
			$this->addControllerMethods($controller, $settings['methods'] ?? []);
		}

		return $builder;
	}

	/**
	 * @param mixed[] $methodsSettings
	 */
	private function addControllerMethods(Controller $controller, array $methodsSettings): void
	{
		foreach ($methodsSettings as $name => $settings) {
			$method = $controller->addMethod($name);
			$method->setId($settings['id'] ?? null);
			$method->setPath($settings['path'] ?? '');
			$method->addHttpMethods($settings['methods'] ?? []);
			$method->addTags($settings['tags'] ?? []);
			$method->setOpenApi($settings['openapi'] ?? []);
			$this->setEndpointParameters($method, $settings['parameters'] ?? []);
			$this->setNegotiations($method, $settings['negotiations'] ?? []);
			$this->setRequestBody($method, $settings['requestBody'] ?? null);
			$this->setResponses($method, $settings['responses'] ?? null);
		}
	}

	/**
	 * @param mixed[] $parametersSettings
	 */
	private function setEndpointParameters(Method $method, array $parametersSettings): void
	{
		foreach ($parametersSettings as $name => $settings) {
			$parameter = $method->addParameter($name, $settings['type'] ?? EndpointParameter::TYPE_STRING);
			$parameter->setIn($settings['in'] ?? EndpointParameter::IN_PATH);
			$parameter->setDescription($settings['description'] ?? null);
			$parameter->setRequired($settings['required'] ?? true);
			$parameter->setAllowEmpty($settings['allowEmpty'] ?? false);
			$parameter->setDeprecated($settings['deprecated'] ?? false);
			$parameter->setEnum($settings['enum'] ?? null);
		}
	}

	/**
	 * @param mixed[] $negotiationsSettings
	 */
	private function setNegotiations(Method $method, array $negotiationsSettings): void
	{
		foreach ($negotiationsSettings as $suffix => $settings) {
			$negotiation = $method->addNegotiation($suffix);
			$negotiation->setDefault($settings['default'] ?? false);
			$negotiation->setRenderer($settings['renderer'] ?? null);
		}
	}

	/**
	 * @param mixed[]|null $requestBodySettings
	 */
	private function setRequestBody(Method $method, ?array $requestBodySettings): void
	{
		if ($requestBodySettings === null) {
			return;
		}

		$requestBody = new EndpointRequestBody();

		$requestBody->setRequired($requestBodySettings['required'] ?? false);
		$requestBody->setDescription($requestBodySettings['description'] ?? null);
		$requestBody->setEntity($requestBodySettings['entity'] ?? null);
		$requestBody->setValidation($requestBodySettings['validation'] ?? true);

		$method->setRequestBody($requestBody);
	}

	/**
	 * @param mixed[]|null $responses
	 */
	private function setResponses(Method $method, ?array $responses): void
	{
		if ($responses === null) {
			return;
		}

		foreach ($responses as $response) {
			$method->addResponse($response['code'], $response['description'] ?? '');
		}
	}

}
