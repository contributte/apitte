<?php declare(strict_types = 1);

/**
 * Test: DI\ApiExtension
 */

use Apitte\Core\Application\Application;
use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\ErrorHandler\PsrLogErrorHandler;
use Apitte\Core\Schema\Schema;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\Controllers\FoobarController;
use Tests\Fixtures\Utils\FakeLogger;

require_once __DIR__ . '/../../bootstrap.php';

// Default
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(JsonDispatcher::class, $container->getService('api.core.dispatcher'));
	Assert::type(Schema::class, $container->getService('api.core.schema'));
	Assert::type(Application::class, $container->getService('api.core.application'));
});

// Annotations
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				FoobarController::class,
			],
		]);
	}, 2);

	/** @var Container $container */
	$container = new $class();

	/** @var Schema $schema */
	$schema = $container->getService('api.core.schema');
	Assert::count(4, $schema->getEndpoints());
	Assert::equal(['GET'], $schema->getEndpoints()[0]->getMethods());
	Assert::equal('/api/v1/foobar/baz1', $schema->getEndpoints()[0]->getMask());
	Assert::equal('#/api/v1/foobar/baz1$#', $schema->getEndpoints()[0]->getPattern());
	Assert::equal([], $schema->getEndpoints()[0]->getParameters());
	Assert::equal(FoobarController::class, $schema->getEndpoints()[0]->getHandler()->getClass());
	Assert::equal('baz1', $schema->getEndpoints()[0]->getHandler()->getMethod());
});

// PsrErrorHandler
test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => true,
			],
			'services' => [
				FakeLogger::class,
			],
		]);
	}, 3);

	/** @var Container $container */
	$container = new $class();

	/** @var IErrorHandler $errorHandler */
	$errorHandler = $container->getService('api.core.errorHandler');
	Assert::true($errorHandler instanceof PsrLogErrorHandler);
});
