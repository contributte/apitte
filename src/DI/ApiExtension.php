<?php

namespace Apitte\Core\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\PhpGenerator\ClassType;

class ApiExtension extends CompilerExtension
{

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

		// Register core plugin
		$this->pm->registerPlugin(new CorePlugin($this));

		// Register all definede plugins
		$this->pm->registerPlugins($config['plugins']);

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
