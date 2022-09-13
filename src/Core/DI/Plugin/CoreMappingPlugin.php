<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Decorator\RequestEntityDecorator;
use Apitte\Core\Decorator\RequestParametersDecorator;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Mapping\Parameter\BooleanTypeMapper;
use Apitte\Core\Mapping\Parameter\DateTimeTypeMapper;
use Apitte\Core\Mapping\Parameter\FloatTypeMapper;
use Apitte\Core\Mapping\Parameter\IntegerTypeMapper;
use Apitte\Core\Mapping\Parameter\StringTypeMapper;
use Apitte\Core\Mapping\RequestEntityMapping;
use Apitte\Core\Mapping\RequestParameterMapping;
use Apitte\Core\Mapping\Validator\IEntityValidator;
use Apitte\Core\Mapping\Validator\NullValidator;
use Apitte\Core\Schema\EndpointParameter;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class CoreMappingPlugin extends Plugin
{

	/** @var array<string, string> */
	private array $defaultTypes = [
		'string' => StringTypeMapper::class,
		'int' => IntegerTypeMapper::class,
		'float' => FloatTypeMapper::class,
		'bool' => BooleanTypeMapper::class,
		'datetime' => DateTimeTypeMapper::class,
	];

	public static function getName(): string
	{
		return 'mapping';
	}

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([
			'types' => Expect::arrayOf('string', 'string'),
			'request' => Expect::structure([
				'validator' => Expect::type('string|array|' . Statement::class)->default(NullValidator::class),
			]),
		]);
	}

	/**
	 * Register services
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('request.parameters.decorator'))
			->setFactory(RequestParametersDecorator::class)
			->addTag(ApiExtension::CORE_DECORATOR_TAG, ['priority' => 100]);

		$builder->addDefinition($this->prefix('request.entity.decorator'))
			->setFactory(RequestEntityDecorator::class)
			->addTag(ApiExtension::CORE_DECORATOR_TAG, ['priority' => 101]);

		$parametersMapping = $builder->addDefinition($this->prefix('request.parameters.mapping'))
			->setFactory(RequestParameterMapping::class);

		foreach ($this->defaultTypes as $type => $mapper) {
			if (!array_key_exists($type, $config->types)) {
				$parametersMapping->addSetup('addMapper', [$type, $mapper]);
			}
		}

		foreach ($config->types as $type => $mapper) {
			$parametersMapping->addSetup('addMapper', [$type, $mapper]);
		}

		$builder->addDefinition($this->prefix('request.entity.mapping.validator'))
			->setType(IEntityValidator::class)
			->setFactory($config->request->validator);

		$builder->addDefinition($this->prefix('request.entity.mapping'))
			->setFactory(RequestEntityMapping::class)
			->addSetup('setValidator', ['@' . $this->prefix('request.entity.mapping.validator')]);
	}

	/**
	 * @return array<string>
	 */
	public function getAllowedTypes(): array
	{
		/** @var array<string> $configuredTypes */
		$configuredTypes = array_keys($this->config->types);
		return array_merge(EndpointParameter::TYPES, $configuredTypes);
	}

}
