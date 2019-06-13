<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Nette\DI\ContainerBuilder;
use Nette\DI\InvalidConfigurationException;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Context;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;
use stdClass;

abstract class AbstractPlugin implements Plugin
{

	/** @var PluginCompiler */
	protected $compiler;

	/** @var stdClass|mixed[] */
	protected $config;

	abstract public static function getName(): string;

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([]);
	}

	public function __construct(PluginCompiler $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * Process and validate config
	 *
	 * @param mixed[] $config
	 */
	public function setupPlugin(array $config = []): void
	{
		$name = $this->compiler->getExtension()->getName() . ' > plugins > ' . static::class;
		$this->setupConfig($this->getConfigSchema(), $config, $name);
	}

	/**
	 * @param mixed[] $config
	 */
	protected function setupConfig(Schema $schema, array $config, string $name): void
	{
		$processor = new Processor();
		$processor->onNewContext[] = function (Context $context) use ($name): void {
			$context->path = [$name];
		};
		try {
			$this->config = $processor->process($schema, $config);
		} catch (ValidationException $exception) {
			throw  new InvalidConfigurationException($exception->getMessage());
		}
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
		return $this->compiler->getExtension()->prefix(static::getName() . '.' . $id);
	}

	protected function extensionPrefix(string $id): string
	{
		return $this->compiler->getExtension()->prefix($id);
	}

	protected function getContainerBuilder(): ContainerBuilder
	{
		return $this->compiler->getExtension()->getContainerBuilder();
	}

}
