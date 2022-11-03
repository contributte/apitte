<?php declare(strict_types = 1);

namespace Apitte\Core\DI;

use Apitte\Core\DI\Plugin\CoreSchemaPlugin;
use Apitte\Core\DI\Plugin\CoreServicesPlugin;
use Apitte\Core\DI\Plugin\PluginManager;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 * @method stdClass getConfig()
 */
class ApiExtension extends CompilerExtension
{

	public const CORE_DECORATOR_TAG = 'apitte.core.decorator';
	public const NEGOTIATION_TRANSFORMER_TAG = 'apitte.negotiator.transformer';
	public const NEGOTIATION_NEGOTIATOR_TAG = 'apitte.negotiation.negotiator';
	public const NEGOTIATION_RESOLVER_TAG = 'apitte.negotiation.resolver';

	/** @var PluginManager */
	private PluginManager $pm;

	public function getConfigSchema(): Schema
	{
		$parameters = $this->getContainerBuilder()->parameters;
		return Expect::structure([
			'catchException' => Expect::bool(true),
			'debug' => Expect::bool($parameters['debugMode'] ?? false),
			'plugins' => Expect::array()->default([
				CoreServicesPlugin::class => [],
				CoreSchemaPlugin::class => [],
			]),
		]);
	}

	public function __construct()
	{
		$this->pm = new PluginManager($this);
	}

	public function loadConfiguration(): void
	{
		$config = $this->config;

		// Register all defined plugins
		$this->pm->loadPlugins($config->plugins);

		// Load services from all plugins
		$this->pm->loadConfigurations();
	}

	public function beforeCompile(): void
	{
		// Decorate services from all plugins
		$this->pm->beforeCompiles();
	}

	public function afterCompile(ClassType $class): void
	{
		// Decorate services from all plugins
		$this->pm->afterCompiles($class);
	}

	public function getCompiler(): Compiler
	{
		return $this->compiler;
	}

	public function getName(): string
	{
		return $this->name;
	}

}
