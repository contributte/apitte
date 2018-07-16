<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\ApiExtension;

class PluginCompiler
{

	/** @var PluginManager */
	protected $manager;

	/** @var ApiExtension */
	protected $extension;

	public function __construct(PluginManager $manager, ApiExtension $extension)
	{
		$this->manager = $manager;
		$this->extension = $extension;
	}

	public function getExtension(): ApiExtension
	{
		return $this->extension;
	}

	public function getPlugin(string $name): ?AbstractPlugin
	{
		$plugins = $this->manager->getPlugins();

		return $plugins[$name]['inst'] ?? null;
	}

	public function getPluginByType(string $class): ?AbstractPlugin
	{
		foreach ($this->manager->getPlugins() as $plugin) {
			if (get_class($plugin['inst']) === $class) return $plugin['inst'];
		}

		return null;
	}

}
