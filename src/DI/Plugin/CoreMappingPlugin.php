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

class CoreMappingPlugin extends AbstractPlugin
{

	public const PLUGIN_NAME = 'mapping';

	/** @var mixed[] */
	protected $defaults = [
		'types' => [
			'string' => StringTypeMapper::class,
			'int' => IntegerTypeMapper::class,
			'float' => FloatTypeMapper::class,
			'bool' => BooleanTypeMapper::class,
			'datetime' => DateTimeTypeMapper::class,
		],
		'request' => [
			'validator' => NullValidator::class,
		],
	];

	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Register services
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->setupConfig($this->defaults, $this->getConfig());

		$builder->addDefinition($this->prefix('request.parameters.decorator'))
			->setFactory(RequestParametersDecorator::class)
			->addTag(ApiExtension::CORE_DECORATOR_TAG, ['priority' => 100]);

		$builder->addDefinition($this->prefix('request.entity.decorator'))
			->setFactory(RequestEntityDecorator::class)
			->addTag(ApiExtension::CORE_DECORATOR_TAG, ['priority' => 101]);

		$parametersMapping = $builder->addDefinition($this->prefix('request.parameters.mapping'))
			->setFactory(RequestParameterMapping::class);

		foreach ($config['types'] as $type => $mapper) {
			$parametersMapping->addSetup('addMapper', [$type, $mapper]);
		}

		$builder->addDefinition($this->prefix('request.entity.mapping.validator'))
			->setType(IEntityValidator::class)
			->setFactory($config['request']['validator']);

		$builder->addDefinition($this->prefix('request.entity.mapping'))
			->setFactory(RequestEntityMapping::class)
			->addSetup('setValidator', ['@' . $this->prefix('request.entity.mapping.validator')]);
	}

}
