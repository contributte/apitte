<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\ResponseMapperValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Mapping\Response\IResponseEntity;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\ResponseMapperValidation;
use Tester\Assert;
use Tests\Fixtures\Mapping\Response\FooEntity;
use Tests\Fixtures\Mapping\Response\InvalidEntity;

// Validate: empty, no error
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addMethod('foo');

	Assert::noError(function () use ($builder): void {
		$validator = new ResponseMapperValidation();
		$validator->validate($builder);
	});
});

// Validate: entity exists, no error
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setResponseMapper(FooEntity::class);

	Assert::noError(function () use ($builder): void {
		$validator = new ResponseMapperValidation();
		$validator->validate($builder);
	});
});

// Validate: entity does not exist
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setResponseMapper('bar');

	Assert::exception(function () use ($builder): void {
		$validator = new ResponseMapperValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, 'Response mapping entity "bar" in "c1::foo()" does not exist"');
});

// Validate: entity is invalid
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setResponseMapper(InvalidEntity::class);

	Assert::exception(
		function () use ($builder): void {
			$validator = new ResponseMapperValidation();
			$validator->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Response mapping entity "%s" in "%s::%s()" does not implement "%s"',
			InvalidEntity::class,
			'c1',
			'foo',
			IResponseEntity::class
		)
	);
});
