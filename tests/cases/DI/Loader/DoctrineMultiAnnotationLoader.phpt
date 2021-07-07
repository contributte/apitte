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
		testMultiControllerResponses($controller);
		testMultiControllerRequestParameters($controller);
		testMultiControllerNegotiations($controller);
	}
});

function testMultiController(Controller $controller): void
{
	Assert::count(2, $controller->getTags());
}

function testMultiControllerRequestParameters(Controller $controller): void
{
	$requestParametersMethod = $controller->getMethods()['requestParameters'];
	Assert::equal('requestParameters', $requestParametersMethod->getName());
	Assert::count(2, $requestParametersMethod->getParameters());

	$firstParameter = $requestParametersMethod->getParameters()['name_value'];

	Assert::equal('name_value', $firstParameter->getName());
	Assert::equal('type_value', $firstParameter->getType());
	Assert::equal('in_value', $firstParameter->getIn());
	Assert::null($firstParameter->getDescription());

	$secondParameter = $requestParametersMethod->getParameters()['name_value_2'];

	Assert::equal('name_value_2', $secondParameter->getName());
	Assert::equal('type_value_2', $secondParameter->getType());
	Assert::equal('in_value_2', $secondParameter->getIn());
	Assert::null($secondParameter->getDescription());
}

function testMultiControllerResponses(Controller $controller): void
{
	$responsesMethod = $controller->getMethods()['responses'];
	Assert::equal('responses', $responsesMethod->getName());
	Assert::count(2, $responsesMethod->getResponses());

	$firstResponse = $responsesMethod->getResponses()['cz'];

	Assert::equal('cz', $firstResponse->getCode());
	Assert::equal('some_description', $firstResponse->getDescription());
	Assert::null($firstResponse->getEntity());

	$secondResponse = $responsesMethod->getResponses()['com'];

	Assert::equal('com', $secondResponse->getCode());
	Assert::equal('some_description_2', $secondResponse->getDescription());
	Assert::null($secondResponse->getEntity());
}

function testMultiControllerNegotiations(Controller $controller): void
{
	$negotiationsMethod = $controller->getMethods()['negotiations'];
	Assert::equal('negotiations', $negotiationsMethod->getName());
	Assert::count(2, $negotiationsMethod->getNegotiations());

	$firstNegotiation = $negotiationsMethod->getNegotiations()[0];

	Assert::same('some_suffix', $firstNegotiation->getSuffix());
	Assert::null($firstNegotiation->getRenderer());

	$secondNegotiation = $negotiationsMethod->getNegotiations()[1];

	Assert::same('some_suffix_2', $secondNegotiation->getSuffix());
	Assert::null($secondNegotiation->getRenderer());
}
