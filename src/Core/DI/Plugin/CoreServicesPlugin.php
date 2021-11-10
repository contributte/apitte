<?php declare(strict_types = 1);

namespace Apitte\Core\DI\Plugin;

use Apitte\Core\Application\Application;
use Apitte\Core\Application\IApplication;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\ErrorHandler\PsrLogErrorHandler;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Handler\ServiceHandler;
use Apitte\Core\Router\IRouter;
use Apitte\Core\Router\SimpleRouter;
use Apitte\Core\Schema\Schema;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\MissingServiceException;
use Psr\Log\LoggerInterface;

class CoreServicesPlugin extends Plugin
{

	public static function getName(): string
	{
		return 'core';
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
			->setType(IDispatcher::class);

		// Catch exception only in debug mode if explicitly enabled
		$catchException = !$globalConfig->debug || $globalConfig->catchException;

		$builder->addDefinition($this->prefix('errorHandler'))
			->setFactory(SimpleErrorHandler::class)
			->setType(IErrorHandler::class)
			->addSetup('setCatchException', [$catchException]);

		$builder->addDefinition($this->prefix('application'))
			->setType(IApplication::class)
			->setFactory(Application::class);

		$builder->addDefinition($this->prefix('router'))
			->setType(IRouter::class)
			->setFactory(SimpleRouter::class);

		$builder->addDefinition($this->prefix('handler'))
			->setType(IHandler::class)
			->setFactory(ServiceHandler::class);

		$builder->addDefinition($this->prefix('schema'))
			->setFactory(Schema::class);
	}

	public function beforePluginCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$errorHandlerDefinition = $builder->getDefinition($this->prefix('errorHandler'));
		assert($errorHandlerDefinition instanceof ServiceDefinition);

		// Set error handler to PsrErrorHandler if logger is available and user didn't change logger himself
		if ($errorHandlerDefinition->getFactory()->getEntity() === SimpleErrorHandler::class) {
			try {
				$loggerDefinition = $builder->getDefinitionByType(LoggerInterface::class);
				$errorHandlerDefinition->setFactory(PsrLogErrorHandler::class, [$loggerDefinition]);
			} catch (MissingServiceException $exception) {
				// No need to handle
			}
		}
	}

}
