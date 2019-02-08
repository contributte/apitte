<?php declare(strict_types = 1);

namespace Apitte\Core\DI;

use Apitte\Core\DI\Plugin\CoreSchemaPlugin;
use Apitte\Core\DI\Plugin\CoreServicesPlugin;
use Apitte\Core\DI\Plugin\PluginManager;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\PhpGenerator\ClassType;

class ApiExtension extends CompilerExtension
{

	public const CORE_DECORATOR_TAG = 'apitte.core.decorator';
	public const NEGOTIATION_TRANSFORMER_TAG = 'apitte.negotiator.transformer';
	public const NEGOTIATION_NEGOTIATOR_TAG = 'apitte.negotiation.negotiator';
	public const NEGOTIATION_RESOLVER_TAG = 'apitte.negotiation.resolver';

	/** @var mixed[] */
	protected $defaults = [
		'catchException' => true,
		'debug' => '%debugMode%',
		'plugins' => [
			CoreServicesPlugin::class => [],
			CoreSchemaPlugin::class => [],
		],
	];

	/** @var PluginManager */
	private $pm;

	public function __construct()
	{
		$this->pm = new PluginManager($this);
	}

	public function loadConfiguration(): void
	{
		// Initialize whole config
		$config = $this->processConfig();

		// Register all defined plugins
		$this->pm->loadPlugins($config['plugins']);

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

	/**
	 * @return mixed[]
	 */
	protected function processConfig(): array
	{
		$config = $this->validateConfig($this->defaults);
		$this->config = Helpers::expand($config, $this->getContainerBuilder()->parameters);

		return $this->config;
	}

}
