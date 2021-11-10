<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\ControllerValidation
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\ControllerValidation;
use Tester\Assert;
use Tests\Fixtures\Controllers\AnnotationFoobarController;

// Validate: success
test(function (): void {
	$validation = new ControllerValidation();
	$builder = new SchemaBuilder();

	$builder->addController(AnnotationFoobarController::class);

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: not an IController
test(function (): void {
	$validation = new ControllerValidation();
	$builder = new SchemaBuilder();

	$builder->addController('c1');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, 'Controller "c1" must implement "Apitte\Core\UI\Controller\IController"');
});
