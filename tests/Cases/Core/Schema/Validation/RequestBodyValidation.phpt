<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\EndpointRequestBody;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\RequestBodyValidation;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;

// Validate: no request, no error
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addMethod('foo');

	Assert::noError(function () use ($builder): void {
		$validator = new RequestBodyValidation();
		$validator->validate($builder);
	});
});

// Validate: entity is empty, no error
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setRequestBody(new EndpointRequestBody());

	Assert::noError(function () use ($builder): void {
		$validator = new RequestBodyValidation();
		$validator->validate($builder);
	});
});

// Validate: entity exists, no error
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$rb1 = new EndpointRequestBody();
	$rb1->setEntity(FooEntity::class);
	$c1m1->setRequestBody($rb1);

	Assert::noError(function () use ($builder): void {
		$validator = new RequestBodyValidation();
		$validator->validate($builder);
	});
});

// Validate: entity does not exist
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$rb1 = new EndpointRequestBody();
	$rb1->setEntity('bar');
	$c1m1->setRequestBody($rb1);

	Assert::exception(function () use ($builder): void {
		$validator = new RequestBodyValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, 'Request entity "bar" in "c1::foo()" does not exist"');
});
