<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\DI\Helpers;
use Apitte\Core\Dispatcher\DecoratedDispatcher;

class CoreDecoratorPlugin extends AbstractPlugin
{

	public const PLUGIN_NAME = 'decorator';

	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Register services
	 */
	public function loadPluginConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition($this->extensionPrefix('core.dispatcher'))
			->setType(DecoratedDispatcher::class)
			->setFactory(DecoratedDispatcher::class);

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
		$manager = $builder->getDefinition($this->prefix('decorator.manager'));

		$requestDecorators = $builder->findByType(IRequestDecorator::class);
		$requestDecorators = Helpers::sortByPriorityInTag(ApiExtension::CORE_DECORATOR_TAG, $requestDecorators);
		foreach ($requestDecorators as $decorator) {
			$manager->addSetup('addRequestDecorator', [$decorator]);
		}

		$responseDecorators = $builder->findByType(IResponseDecorator::class);
		$responseDecorators = Helpers::sortByPriorityInTag(ApiExtension::CORE_DECORATOR_TAG, $responseDecorators);
		foreach ($responseDecorators as $decorator) {
			$manager->addSetup('addResponseDecorator', [$decorator]);
		}

		$errorDecorators = $builder->findByType(IErrorDecorator::class);
		$errorDecorators = Helpers::sortByPriorityInTag(ApiExtension::CORE_DECORATOR_TAG, $errorDecorators);
		foreach ($errorDecorators as $decorator) {
			$manager->addSetup('addErrorDecorator', [$decorator]);
		}
	}

}
