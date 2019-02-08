<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\Dispatcher\WrappedDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\ErrorHandler\PsrErrorHandler;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\ErrorHandler\TracyErrorHandler;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Handler\ServiceHandler;
use Apitte\Core\Router\IRouter;
use Apitte\Core\Router\SimpleRouter;
use Apitte\Core\Schema\Schema;
use Psr\Log\LoggerInterface;
use Tracy\ILogger;

class CoreServicesPlugin extends AbstractPlugin
{

	public const PLUGIN_NAME = 'core';

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
		// Receive container builder
		$builder = $this->getContainerBuilder();
		$globalConfig = $this->compiler->getExtension()->getConfig();

		$builder->addDefinition($this->prefix('dispatcher'))
			->setFactory(JsonDispatcher::class)
			->setAutowired(false);

		// Catch exception only in debug mode if explicitly enabled
		$catchException = !$globalConfig['debug'] || $globalConfig['catchException'];

		$errorHandler = $builder->addDefinition($this->prefix('errorHandler'))
			->setFactory(SimpleErrorHandler::class)
			->setType(IErrorHandler::class)
			->addSetup('setCatchException', [$catchException]);

		// Set handler with logging, if logger available
		if ($builder->findByType(ILogger::class) !== []) {
			$errorHandler->setFactory(TracyErrorHandler::class);
		} elseif ($builder->findByType(LoggerInterface::class) !== []) {
			$errorHandler->setFactory(PsrErrorHandler::class);
		}

		$builder->addDefinition($this->prefix('dispatcher.wrapper'))
			->setFactory(WrappedDispatcher::class, ['@' . $this->prefix('dispatcher')]);

		$builder->addDefinition($this->prefix('router'))
			->setType(IRouter::class)
			->setFactory(SimpleRouter::class);

		$builder->addDefinition($this->prefix('handler'))
			->setType(IHandler::class)
			->setFactory(ServiceHandler::class);

		$builder->addDefinition($this->prefix('schema'))
			->setFactory(Schema::class);
	}

}
