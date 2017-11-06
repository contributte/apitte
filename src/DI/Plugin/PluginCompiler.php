<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\ApiExtension;

class PluginCompiler
{

	/** @var PluginManager */
	protected $manager;

	/** @var ApiExtension */
	protected $extension;

	/**
	 * @param PluginManager $manager
	 * @param ApiExtension $extension
	 */
	public function __construct(PluginManager $manager, ApiExtension $extension)
	{
		$this->manager = $manager;
		$this->extension = $extension;
	}

	/**
	 * @return ApiExtension
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @param string $name
	 * @return AbstractPlugin|NULL
	 */
	public function getPlugin($name)
	{
		$plugins = $this->manager->getPlugins();

		return isset($plugins[$name]) ? $plugins[$name] : NULL;
	}

	/**
	 * @param string $class
	 * @return AbstractPlugin|NULL
	 */
	public function getPluginByType($class)
	{
		foreach ($this->manager->getPlugins() as $plugin) {
			if (get_class($plugin['inst']) === $class) return $plugin;
		}

		return NULL;
	}

}
