<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Nette\DI\ContainerBuilder;
use Nette\PhpGenerator\ClassType;

abstract class AbstractPlugin implements Plugin
{

	/** @var PluginCompiler */
	protected $compiler;

	/** @var string */
	protected $name;

	/** @var mixed[] */
	protected $config = [];

	/** @var mixed[] */
	protected $defaults = [];

	public function __construct(PluginCompiler $compiler)
	{
		$this->compiler = $compiler;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed[]
	 */
	public function getConfig(): array
	{
		return $this->config;
	}

	/**
	 * Process and validate config
	 *
	 * @param mixed[] $config
	 */
	public function setupPlugin(array $config = []): void
	{
		if (!$this->defaults) return;
		$this->setupConfig($this->defaults, $config);
	}

	public function loadPluginConfiguration(): void
	{
	}

	public function beforePluginCompile(): void
	{
	}

	public function afterPluginCompile(ClassType $class): void
	{
	}

	protected function prefix(string $id): string
	{
		return $this->compiler->getExtension()->prefix($this->name . '.' . $id);
	}

	protected function extensionPrefix(string $id): string
	{
		return $this->compiler->getExtension()->prefix($id);
	}

	protected function getContainerBuilder(): ContainerBuilder
	{
		return $this->compiler->getExtension()->getContainerBuilder();
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $config
	 * @return mixed[]
	 */
	protected function setupConfig(array $expected, array $config): array
	{
		return $this->config = $this->compiler->getExtension()->validateConfig($expected, $config, $this->name);
	}

}
