<?php

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Dispatcher\ApiDispatcher;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Handler\DecoratedServiceHandler;
use Apitte\Core\Handler\IHandler;
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
			->setFactory(DecoratedServiceHandler::class);

		$builder->addDefinition($this->prefix('schema'))
			->setClass(ApiSchema::class);
	}

}
