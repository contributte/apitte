<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Loader;

use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\Builder\Controller\Method;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;

class NeonLoader implements ILoader
{

	/** @var mixed[] */
	private $schema = [];

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
			$schemaController = $builder->addController($class);
			$schemaController->setId($settings['id'] ?? null);
			$schemaController->setPath($settings['path']);
			$this->addControllerMethods($schemaController, $settings);
			$schemaController->addTags($settings['tags'] ?? []);
			$schemaController->setGroupIds($settings['groupIds'] ?? []);
			$schemaController->setGroupPaths($settings['groupPaths'] ?? []);
		}
		return $builder;
	}

	/**
	 * @param mixed[] $settings
	 */
	private function addControllerMethods(Controller $schemaController, array $settings): void
	{
		if (!isset($settings['methods'])) {
			return;
		}
		foreach ($settings['methods'] as $methodName => $methodSettings) {
			$method = $schemaController->addMethod($methodName);
			$method->setId($methodSettings['id'] ?? null);
			$method->addMethods($methodSettings['methods'] ?? []);
			$method->setPath($methodSettings['path']);
			$method->setDescription($methodSettings['description'] ?? '');
			$method->addTags($methodSettings['tags'] ?? []);
			$this->setMethodParameters($method, $methodSettings);
		}
	}

	/**
	 * @param mixed[] $methodSettings
	 */
	private function setMethodParameters(Method $method, array $methodSettings): void
	{
		if (!isset($methodSettings['parameters'])) {
			return;
		}
		foreach ($methodSettings['parameters'] as $parameterName => $parameterSettings) {
			$parameter = $method->addParameter($parameterName, $parameterSettings['type'] ?? EndpointParameter::TYPE_STRING);
			$parameter->setIn($parameterSettings['in'] ?? EndpointParameter::IN_PATH);
			$parameter->setDescription($parameterSettings['description'] ?? null);
			$parameter->setRequired(isset($parameterSettings['required']) ? (bool) $parameterSettings['required'] : true);
			$parameter->setAllowEmpty(isset($parameterSettings['allowEmpty']) ? (bool) $parameterSettings['allowEmpty'] : false);
			$parameter->setDeprecated(isset($parameterSettings['deprecated']) ? (bool) $parameterSettings['deprecated'] : false);
		}
	}

}
