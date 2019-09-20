<?php declare(strict_types = 1);

/**
 * Test: DI\Loader\DoctrineAnnotationLoader
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\UI\Controller\IController;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use Tester\Assert;
use Tests\Fixtures\Controllers\FoobarController;

// Check if controller is found
test(function (): void {
	$builder = Mockery::mock(ContainerBuilder::class);
	$builder->shouldReceive('findByType')
		->once()
		->with(IController::class)
		->andReturnUsing(function (): array {
			$controllers = [];
			$controllers[] = $c1 = new ServiceDefinition();
			$c1->setClass(FoobarController::class);

			return $controllers;
		});

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);

	Mockery::close();
});

// Parse annotations
test(function (): void {
	$builder = new ContainerBuilder();
	$builder->addDefinition('foobar')
		->setClass(FoobarController::class);

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);
	Assert::count(1, $schemaBuilder->getControllers());

	$controllers = $schemaBuilder->getControllers();
	$controller = array_pop($controllers);

	Assert::equal(FoobarController::class, $controller->getClass());
	Assert::equal('/foobar', $controller->getPath());
	Assert::equal('foobar', $controller->getId());

	Assert::count(4, $controller->getMethods());

	Assert::equal('baz1', $controller->getMethods()['baz1']->getName());
	Assert::equal('/baz1', $controller->getMethods()['baz1']->getPath());
	Assert::equal(['GET'], $controller->getMethods()['baz1']->getHttpMethods());
	Assert::equal('baz1', $controller->getMethods()['baz1']->getId());

	Assert::equal('baz2', $controller->getMethods()['baz2']->getName());
	Assert::equal('/baz2', $controller->getMethods()['baz2']->getPath());
	Assert::equal(['GET', 'POST'], $controller->getMethods()['baz2']->getHttpMethods());

	Assert::equal(
		[
			'foo' => ['bar' => 'baz'],
			'lorem' => ['ipsum', 'dolor', 'sit', 'amet'],
		],
		$controller->getMethods()['openapi']->getOpenApi()
	);

	Assert::equal(['testapi'], $controller->getGroupIds());
	Assert::equal(['/api', '/v1'], $controller->getGroupPaths());
});
