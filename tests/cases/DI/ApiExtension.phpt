<?php

/**
 * Test: DI\ApiExtension
 */

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Schema\ApiSchema;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\Controllers\FoobarController;

require_once __DIR__ . '/../../bootstrap.php';

// Default
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => TRUE,
			],
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(IDispatcher::class, $container->getService('api.core.dispatcher'));
	Assert::type(ApiSchema::class, $container->getService('api.core.schema'));
});

// Annotations
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('api', new ApiExtension());
		$compiler->addConfig([
			'parameters' => [
				'debugMode' => TRUE,
			],
			'services' => [
				FoobarController::class,
			],
		]);
	}, 2);

	/** @var Container $container */
	$container = new $class();

	/** @var ApiSchema $schema */
	$schema = $container->getService('api.core.schema');
	Assert::count(3, $schema->getEndpoints());
	Assert::equal(['GET'], $schema->getEndpoints()[0]->getMethods());
	Assert::equal('/api/v1/foobar/baz1', $schema->getEndpoints()[0]->getMask());
	Assert::equal('#/api/v1/foobar/baz1$/?\z#A', $schema->getEndpoints()[0]->getPattern());
	Assert::equal([], $schema->getEndpoints()[0]->getParameters());
	Assert::equal(FoobarController::class, $schema->getEndpoints()[0]->getHandler()->getClass());
	Assert::equal('baz1', $schema->getEndpoints()[0]->getHandler()->getMethod());
});
