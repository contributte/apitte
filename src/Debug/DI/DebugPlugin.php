<?php declare(strict_types = 1);

namespace Apitte\Debug\DI;

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\DI\Plugin\CoreSchemaPlugin;
use Apitte\Core\DI\Plugin\Plugin;
use Apitte\Core\Exception\Logical\InvalidDependencyException;
use Apitte\Debug\Negotiation\Transformer\DebugDataTransformer;
use Apitte\Debug\Negotiation\Transformer\DebugTransformer;
use Apitte\Debug\Schema\Serialization\DebugSchemaDecorator;
use Apitte\Debug\Tracy\BlueScreen\ApiBlueScreen;
use Apitte\Debug\Tracy\BlueScreen\ValidationBlueScreen;
use Apitte\Debug\Tracy\Panel\ApiPanel;
use Apitte\Negotiation\DI\NegotiationPlugin;
use Nette\DI\ContainerBuilder;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Tracy\Debugger;

/**
 * @property-read stdClass $config
 */
class DebugPlugin extends Plugin
{

	public static function getName(): string
	{
		return 'debug';
	}

	protected function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::structure([
				'panel' => Expect::bool(false),
				'negotiation' => Expect::bool(true),
			]),
		]);
	}

	/**
	 * Register services
	 */
	public function loadPluginConfiguration(): void
	{
		if (!class_exists(Debugger::class)) {
			throw InvalidDependencyException::missing(Debugger::class, 'tracy/tracy');
		}

		$builder = $this->getContainerBuilder();
		$global = $this->compiler->getExtension()->getConfig();
		$config = $this->config;

		if (!$global->debug) {
			return;
		}

		if ($config->debug->panel) {
			$builder->addDefinition($this->prefix('panel'))
				->setFactory(ApiPanel::class);
		}

		if ($config->debug->negotiation) {
			$this->loadNegotiationDebugConfiguration();
		}

		// BlueScreen - runtime
		ApiBlueScreen::register(Debugger::getBlueScreen());
		ValidationBlueScreen::register(Debugger::getBlueScreen());
	}

	protected function loadNegotiationDebugConfiguration(): void
	{
		// Skip if plugin apitte/negotiation is not loaded
		if ($this->compiler->getPluginByType(NegotiationPlugin::class) === null) {
			return;
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('transformer.debug'))
			->setFactory(DebugTransformer::class)
			->addTag(ApiExtension::NEGOTIATION_TRANSFORMER_TAG, ['suffix' => 'debug']);

		$builder->addDefinition($this->prefix('transformer.debugdata'))
			->setFactory(DebugDataTransformer::class)
			->addTag(ApiExtension::NEGOTIATION_TRANSFORMER_TAG, ['suffix' => 'debugdata']);

		// Setup debug schema decorator
		CoreSchemaPlugin::$decorators['debug'] = new DebugSchemaDecorator();
	}

	public function afterPluginCompile(ClassType $class): void
	{
		$global = $this->compiler->getExtension()->getConfig();
		$config = $this->config;

		$initialize = $class->getMethod('initialize');

		$initialize->addBody('?::register($this->getService(?));', [ContainerBuilder::literal(ApiBlueScreen::class), 'tracy.blueScreen']);
		$initialize->addBody('?::register($this->getService(?));', [ContainerBuilder::literal(ValidationBlueScreen::class), 'tracy.blueScreen']);

		if ($global->debug && $config->debug->panel) {
			$initialize->addBody('$this->getService(?)->addPanel($this->getService(?));', ['tracy.bar', $this->prefix('panel')]);
		}
	}

}
