<?php

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

	const CORE_DECORATOR_TAG = 'apitte.core.decorator';
	const NEGOTIATION_TRANSFORMER_TAG = 'apitte.negotiator.transformer';
	const NEGOTIATION_NEGOTIATOR_TAG = 'apitte.negotiation.negotiator';
	const MAPPING_DECORATOR_TAG = 'apitte.mapping.decorator';
	const MAPPING_HANDLER_DECORATOR_TAG = 'apitte.mapping.handler.decorator';

	/** @var array */
	protected $defaults = [
		'debug' => '%debugMode%',
		'plugins' => [],
	];

	/** @var PluginManager */
	private $pm;

	/**
	 * Create extension
	 */
	public function __construct()
	{
		$this->pm = new PluginManager($this);
	}

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		// Initialize whole config
		$config = $this->processConfig();

		// Register core plugin(s)
		$this->pm->loadPlugin(CoreServicesPlugin::class);
		$this->pm->loadPlugin(CoreSchemaPlugin::class);

		// Register all definede plugins
		$this->pm->loadPlugins($config['plugins']);

		// Load services from all plugins
		$this->pm->loadConfigurations();
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforeCompile()
	{
		// Decorate services from all plugins
		$this->pm->beforeCompiles();
	}

	/**
	 * Decorate PHP code
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompile(ClassType $class)
	{
		// Decorate services from all plugins
		$this->pm->afterCompiles($class);
	}

	/**
	 * @return Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @return array
	 */
	protected function processConfig()
	{
		$config = $this->validateConfig($this->defaults);
		$this->config = Helpers::expand($config, $this->getContainerBuilder()->parameters);

		return $this->config;
	}

}
