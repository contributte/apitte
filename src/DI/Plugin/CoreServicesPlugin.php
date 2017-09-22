<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Dispatcher\ApiDispatcher;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Handler\DecoratedServiceHandler;
use Apitte\Core\Handler\Decorator\IRequestDecorator;
use Apitte\Core\Handler\Decorator\RequestParameterDecorator;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Handler\ServiceHandler;
use Apitte\Core\Router\ApiRouter;
use Apitte\Core\Router\IRouter;
use Apitte\Core\Schema\ApiSchema;

class CoreServicesPlugin extends AbstractPlugin
{

	const PLUGIN_NAME = 'core';

	/**
	 * @param PluginCompiler $compiler
	 */
	public function __construct(PluginCompiler $compiler)
	{
		parent::__construct($compiler);
		$this->name = self::PLUGIN_NAME;
	}

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadPluginConfiguration()
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('dispatcher'))
			->setClass(IDispatcher::class)
			->setFactory(ApiDispatcher::class);

		$builder->addDefinition($this->prefix('router'))
			->setClass(IRouter::class)
			->setFactory(ApiRouter::class);

		$builder->addDefinition($this->prefix('handler'))
			->setClass(IHandler::class)
			->setFactory(ServiceHandler::class);

		$builder->addDefinition($this->prefix('schema'))
			->setClass(ApiSchema::class);

		$builder->addDefinition($this->prefix('decorator.requestparameters'))
			->setClass(RequestParameterDecorator::class);
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforePluginCompile()
	{
		// Receive container builder
		$builder = $this->getContainerBuilder();

		// Find all decorators
		$requestDecorators = $builder->findByType(IRequestDecorator::class);
		$responseDecorators = $builder->findByType(IRequestDecorator::class);

		// Setup DecoratedServiceHandler only if
		// some decorators are provided
		if ($requestDecorators || $responseDecorators) {
			$builder->getDefinition($this->prefix('handler'))
				->setFactory(DecoratedServiceHandler::class)
				->addSetup('addDecorators', [$requestDecorators])
				->addSetup('addDecorators', [$responseDecorators]);
		}
	}

}
