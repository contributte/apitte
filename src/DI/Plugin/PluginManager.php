<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\PhpGenerator\ClassType;

final class PluginManager
{

	/** @var PluginCompiler */
	private $compiler;

	/** @var AbstractPlugin[] */
	private $plugins = [];

	/**
	 * @param ApiExtension $extension
	 */
	public function __construct(ApiExtension $extension)
	{
		$this->compiler = new PluginCompiler($this, $extension);
	}

	/**
	 * PLUGINS *****************************************************************
	 */

	/**
	 * @param AbstractPlugin $plugin
	 * @param array $config
	 * @return AbstractPlugin
	 */
	public function registerPlugin(AbstractPlugin $plugin, array $config = [])
	{
		// Register plugin
		$this->plugins[$plugin->getName()] = [
			'inst' => $plugin,
			'config' => $config,
		];

		return $plugin;
	}

	/**
	 * @param array $plugins
	 * @return void
	 */
	public function loadPlugins(array $plugins)
	{
		foreach ($plugins as $class => $config) {
			if (!class_exists($class)) {
				throw new InvalidStateException(sprintf('Plugin class "%s" not found', $class));
			}

			$this->loadPlugin($class, (array) $config);
		}
	}

	/**
	 * @param string $class
	 * @param array $config
	 * @return void
	 */
	public function loadPlugin($class, array $config = [])
	{
		if (!is_subclass_of($class, AbstractPlugin::class)) {
			throw new InvalidStateException(sprintf('Plugin class "%s" is not subclass of "%s"', $class, AbstractPlugin::class));
		}

		/** @var AbstractPlugin $plugin */
		$plugin = new $class($this->compiler);

		// Register plugin
		$this->registerPlugin($plugin, $config);
	}

	/**
	 * @return AbstractPlugin[]
	 */
	public function getPlugins()
	{
		return $this->plugins;
	}

	/**
	 * EXTENSION ***************************************************************
	 */

	/**
	 * Register services from all plugins
	 *
	 * @return void
	 */
	public function loadConfigurations()
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->setupPlugin($plugin['config']);
			$plugin['inst']->loadPluginConfiguration();
		}
	}

	/**
	 * Register services from all plugins
	 *
	 * @return void
	 */
	public function beforeCompiles()
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->beforePluginCompile();
		}
	}

	/**
	 * Decorate PHP code
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompiles(ClassType $class)
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->afterPluginCompile($class);
		}
	}

}
