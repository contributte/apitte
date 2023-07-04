<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\DI\Loader\DoctrineAnnotationLoader;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\RequestParameterValidation;
use Contributte\Tester\Toolkit;
use Nette\DI\ContainerBuilder;
use Tester\Assert;
use Tests\Fixtures\Controllers\Mixed\AnnotationAttributeController;
use Tests\Fixtures\Controllers\Mixed\AttributesOnlyController;
use Tests\Fixtures\Controllers\Mixed\PathAndRequestParamsController;

// Parse annotations
Toolkit::test(function (): void {
	$builder = new ContainerBuilder();
	$builder->addDefinition('first_controller')
		->setType(AnnotationAttributeController::class);

	$builder->addDefinition('second_controller')
		->setType(PathAndRequestParamsController::class);

	$builder->addDefinition('third_controller')
		->setType(AttributesOnlyController::class);

	$loader = new DoctrineAnnotationLoader($builder);
	$schemaBuilder = $loader->load(new SchemaBuilder());
	Assert::type(SchemaBuilder::class, $schemaBuilder);

	$controllers = $schemaBuilder->getControllers();
	Assert::count(3, $controllers);

	$requestParameterValidation = new RequestParameterValidation();
	$requestParameterValidation->validate($schemaBuilder);
});
