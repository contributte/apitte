<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\DI\Loader\AttributeLoader;
use Apitte\Core\Schema\Builder\Controller\Controller;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\SchemaBuilder;
use Contributte\Tester\Toolkit;
use Nette\DI\ContainerBuilder;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationMultiController;
use Tests\Fixtures\Controllers\AttributeMultiController;

// Parse attributes
Toolkit::test(function (): void {
	$builder = new ContainerBuilder();
	$builder->addDefinition('annotation_multi_controller')
		->setType(AnnotationMultiController::class);

	$builder->addDefinition('attribute_multi_controller')
		->setType(AttributeMultiController::class);

	$loader = new AttributeLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());

	Assert::type(SchemaBuilder::class, $schemaBuilder);

	$controllers = $schemaBuilder->getControllers();

	foreach ($controllers as $controller) {
		testTags($controller);
		testResponses($controller);
		testRequestParameters($controller);
		testNegotiations($controller);
	}
});

function testTags(Controller $controller): void
{
	Assert::count(2, $controller->getTags());

	$firstTagValue = $controller->getTags()['nice'];
	Assert::same('yes', $firstTagValue);

	$secondTagValue = $controller->getTags()['one'];
	Assert::same('no', $secondTagValue);
}

function testRequestParameters(Controller $controller): void
{
	$requestParametersMethod = $controller->getMethods()['requestParameters'];
	Assert::equal('requestParameters', $requestParametersMethod->getName());
	Assert::count(2, $requestParametersMethod->getParameters());

	$firstParameter = $requestParametersMethod->getParameters()['name_value'];

	Assert::equal('name_value', $firstParameter->getName());
	Assert::equal('type_value', $firstParameter->getType());
	Assert::equal(EndpointParameter::IN_PATH, $firstParameter->getIn());
	Assert::null($firstParameter->getDescription());

	$secondParameter = $requestParametersMethod->getParameters()['name_value_2'];

	Assert::equal('name_value_2', $secondParameter->getName());
	Assert::equal('type_value_2', $secondParameter->getType());
	Assert::equal(EndpointParameter::IN_QUERY, $secondParameter->getIn());
	Assert::null($secondParameter->getDescription());
}

function testResponses(Controller $controller): void
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

function testNegotiations(Controller $controller): void
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
