<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\RequestMapperValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\RequestMapperValidation;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;

// Validate: empty, no error
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addMethod('foo');

	Assert::noError(function () use ($builder): void {
		$validator = new RequestMapperValidation();
		$validator->validate($builder);
	});
});

// Validate: entity exists, no error
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setRequestMapper(FooEntity::class);

	Assert::noError(function () use ($builder): void {
		$validator = new RequestMapperValidation();
		$validator->validate($builder);
	});
});

// Validate: entity does not exist
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setRequestMapper('bar');

	Assert::exception(function () use ($builder): void {
		$validator = new RequestMapperValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, 'Request mapping entity "bar" in "c1::foo()" does not exist"');
});
