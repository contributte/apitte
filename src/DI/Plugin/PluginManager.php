<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Nette\PhpGenerator\ClassType;

final class PluginManager
{

	/** @var PluginCompiler */
	private $compiler;

	/** @var mixed[] */
	private $plugins = [];

	public function __construct(ApiExtension $extension)
	{
		$this->compiler = new PluginCompiler($this, $extension);
	}

	/**
	 * @param mixed[] $config
	 */
	public function registerPlugin(AbstractPlugin $plugin, array $config = []): AbstractPlugin
	{
		// Register plugin
		$this->plugins[$plugin::getName()] = [
			'inst' => $plugin,
			'config' => $config,
		];

		return $plugin;
	}

	/**
	 * @param mixed[] $plugins
	 */
	public function loadPlugins(array $plugins): void
	{
		foreach ($plugins as $class => $config) {
			$this->loadPlugin($class, (array) $config);
		}
	}

	/**
	 * @param mixed[] $config
	 */
	public function loadPlugin(string $class, array $config = []): void
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
	 * @return mixed[]
	 */
	public function getPlugins(): array
	{
		return $this->plugins;
	}

	/**
	 * Register services from all plugins
	 */
	public function loadConfigurations(): void
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->setupPlugin($plugin['config']);
			$plugin['inst']->loadPluginConfiguration();
		}
	}

	/**
	 * Register services from all plugins
	 */
	public function beforeCompiles(): void
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->beforePluginCompile();
		}
	}

	/**
	 * Decorate PHP code
	 */
	public function afterCompiles(ClassType $class): void
	{
		foreach ($this->plugins as $plugin) {
			$plugin['inst']->afterPluginCompile($class);
		}
	}

}
