<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;
use Apitte\Core\Schema\Builder\Controller\MethodRequest;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;

class NeonLoader implements ILoader
{

	/** @var mixed[] */
	private $schema;

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
			$method->setDescription($settings['description'] ?? '');
			$method->addHttpMethods($settings['methods'] ?? []);
			$method->addTags($settings['tags'] ?? []);
			$method->setOpenApi($settings['openapi'] ?? []);
			$this->setMethodParameters($method, $settings['parameters'] ?? []);
			$this->setNegotiations($method, $settings['negotiations'] ?? []);
			$this->setRequest($method, $settings['request'] ?? null);
			$this->setResponses($method, $settings['responses'] ?? null);
		}
	}

	/**
	 * @param mixed[] $parametersSettings
	 */
	private function setMethodParameters(Method $method, array $parametersSettings): void
	{
		foreach ($parametersSettings as $name => $settings) {
			$parameter = $method->addParameter($name, $settings['type'] ?? EndpointParameter::TYPE_STRING);
			$parameter->setIn($settings['in'] ?? EndpointParameter::IN_PATH);
			$parameter->setDescription($settings['description'] ?? null);
			$parameter->setRequired($settings['required'] ?? true);
			$parameter->setAllowEmpty($settings['allowEmpty'] ?? false);
			$parameter->setDeprecated($settings['deprecated'] ?? false);
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
	 * @param mixed[]|null $requestSettings
	 */
	private function setRequest(Method $method, ?array $requestSettings): void
	{
		if ($requestSettings === null) {
			return;
		}

		$request = new MethodRequest();

		$request->setRequired($requestSettings['required'] ?? false);
		$request->setDescription($requestSettings['description'] ?? null);
		$request->setEntity($requestSettings['entity'] ?? null);
		$request->setValidation($requestSettings['validation'] ?? true);

		$method->setRequest($request);
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
