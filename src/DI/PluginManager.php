<?php

namespace Apitte\Core\DI;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\PhpGenerator\ClassType;

final class PluginManager
{

	/** @var ApiExtension */
	private $extension;

	/** @var array */
	private $plugins = [];

	/**
	 * @param ApiExtension $extension
	 */
	public function __construct(ApiExtension $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * PLUGINS *****************************************************************
	 */

	/**
	 * @param string $class
	 * @param array $config
	 * @return AbstractPlugin
	 */
	public function registerPlugin($class, array $config = [])
	{
		if (!is_subclass_of($class, AbstractPlugin::class)) {
			throw new InvalidStateException(sprintf('Plugin class "%s" is not subclass of "%s"', $class, AbstractPlugin::class));
		}

		/** @var AbstractPlugin $plugin */
		$plugin = new $class($this->extension);

		// Register plugin
		$this->plugins[$plugin->getName()] = (object) [
			'inst' => $plugin,
			'config' => $config,
		];

		return $plugin;
	}

	/**
	 * @param array $plugins
	 */
	public function registerPlugins(array $plugins)
	{
		foreach ($plugins as $class => $config) {
			if (!class_exists($class)) {
				throw new InvalidStateException(sprintf('Plugin class "%s" not found', $class));
			}

			$this->registerPlugin($class, (array) $config);
		}
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
			$plugin->inst->setupPlugin($plugin->config);
			$plugin->inst->loadPluginConfiguration();
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
			$plugin->inst->beforePluginCompile();
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
			$plugin->inst->afterPluginCompile($class);
		}
	}

}
