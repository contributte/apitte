<?php

/**
 * Test: DI\ApiExtension
 */

use Apitte\Core\DI\ApiExtension;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Schema\ApiSchema;
use Fixtures\Controllers\FoobarController;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

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

	Assert::type(IDispatcher::class, $container->getService('api.dispatcher'));
	Assert::type(ApiSchema::class, $container->getService('api.schema'));
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
	$schema = $container->getService('api.schema');
	Assert::count(3, $schema->getEndpoints());
	Assert::equal(['GET'], $schema->getEndpoints()[0]->getMethods());
	Assert::equal('/foobar/baz1', $schema->getEndpoints()[0]->getMask());
	Assert::equal('#/foobar/baz1$/?\z#A', $schema->getEndpoints()[0]->getPattern());
	Assert::equal([], $schema->getEndpoints()[0]->getParameters());
	Assert::equal('Fixtures\Controllers\FoobarController', $schema->getEndpoints()[0]->getHandler()->getClass());
	Assert::equal('baz1', $schema->getEndpoints()[0]->getHandler()->getMethod());
});
