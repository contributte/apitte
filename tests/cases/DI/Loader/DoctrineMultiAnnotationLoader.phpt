<?php declare(strict_types = 1);

/**
 * Test: DI\Loader\DoctrineAnnotationLoader for multiple annotations of one type
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\SchemaBuilder;
use Nette\DI\ContainerBuilder;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationMultiController;
use Tests\Fixtures\Controllers\AttributeMultiController;

// Parse annotations
test(function (): void {
	$builder = new ContainerBuilder();
	$builder->addDefinition('annotation_multi_controller')
		->setType(AnnotationMultiController::class);

	// include attribute controller only on PHP 8.0 and up
	if (PHP_VERSION_ID >= 80000) {
		$builder->addDefinition('attribute_multi_controller')
			->setType(AttributeMultiController::class);
	}

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);

	$controllers = $schemaBuilder->getControllers();

	foreach ($controllers as $controller) {
		testMultiController($controller);
	}
});

function testMultiController(Controller $controller): void
{
	Assert::count(2, $controller->getTags());

	$responsesMethod = $controller->getMethods()['responses'];
	Assert::equal('responses', $responsesMethod->getName());
	Assert::count(2, $responsesMethod->getResponses());

	$requestParametersMethod = $controller->getMethods()['requestParameters'];
	Assert::equal('requestParameters', $requestParametersMethod->getName());
	Assert::count(2, $requestParametersMethod->getParameters());

	$negotiationsMethod = $controller->getMethods()['negotiations'];
	Assert::equal('negotiations', $negotiationsMethod->getName());
	Assert::count(2, $negotiationsMethod->getNegotiations());
}
