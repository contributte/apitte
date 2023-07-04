<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\UI\Controller\IController;
use Contributte\Tester\Toolkit;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationFoobarController;
use Tests\Fixtures\Controllers\ApiV1Controller;
use Tests\Fixtures\Controllers\AttributeFoobarController;

// Check if controller is found
Toolkit::test(function (): void {
	$builder = Mockery::mock(ContainerBuilder::class);
	$builder->shouldReceive('findByType')
		->once()
		->with(IController::class)
		->andReturnUsing(function (): array {
			$controllers = [];
			$controllers[] = $c1 = new ServiceDefinition();
			$c1->setType(AnnotationFoobarController::class);

			return $controllers;
		});

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);

	Mockery::close();
});

// Parse annotations
Toolkit::test(function (): void {
	$builder = new ContainerBuilder();
	$builder->addDefinition('annotation_controller')
		->setType(AnnotationFoobarController::class);

	// include attribute controller only on PHP 8.0 and up
	if (PHP_VERSION_ID >= 80000) {
		$builder->addDefinition('attribute_controller')
			->setType(AttributeFoobarController::class);
	}

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);
	Assert::count(count($builder->findByType(ApiV1Controller::class)), $schemaBuilder->getControllers());

	$controllers = $schemaBuilder->getControllers();

	foreach ($controllers as $controller) {
		testController($controller);
	}
});

function testController(Controller $controller): void
{
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
}
