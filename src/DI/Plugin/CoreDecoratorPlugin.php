<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\DI\Helpers;
use Apitte\Core\Dispatcher\DecoratedDispatcher;
use Nette\DI\ServiceDefinition;

class CoreDecoratorPlugin extends Plugin
{

	public static function getName(): string
	{
		return 'decorator';
	}

	/**
	 * Register services
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$dispatcherDefinition = $builder->getDefinition($this->extensionPrefix('core.dispatcher'));
		assert($dispatcherDefinition instanceof ServiceDefinition);
		$dispatcherDefinition->setFactory(DecoratedDispatcher::class);

		$builder->addDefinition($this->prefix('decorator.manager'))
			->setFactory(DecoratorManager::class);
	}

	/**
	 * Decorate services
	 */
	public function beforePluginCompile(): void
	{
		$this->compileDecorators();
	}

	protected function compileDecorators(): void
	{
		$builder = $this->getContainerBuilder();
		$managerDefinition = $builder->getDefinition($this->prefix('decorator.manager'));
		assert($managerDefinition instanceof ServiceDefinition);

		$requestDecoratorDefinitions = $builder->findByType(IRequestDecorator::class);
		$requestDecoratorDefinitions = Helpers::sortByPriorityInTag(ApiExtension::CORE_DECORATOR_TAG, $requestDecoratorDefinitions);
		foreach ($requestDecoratorDefinitions as $decoratorDefinition) {
			$managerDefinition->addSetup('addRequestDecorator', [$decoratorDefinition]);
		}

		$responseDecoratorDefinitions = $builder->findByType(IResponseDecorator::class);
		$responseDecoratorDefinitions = Helpers::sortByPriorityInTag(ApiExtension::CORE_DECORATOR_TAG, $responseDecoratorDefinitions);
		foreach ($responseDecoratorDefinitions as $decoratorDefinition) {
			$managerDefinition->addSetup('addResponseDecorator', [$decoratorDefinition]);
		}
	}

}
