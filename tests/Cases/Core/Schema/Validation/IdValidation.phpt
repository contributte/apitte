<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\IdValidation;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Validate: success
Toolkit::test(function (): void {
	$validation = new IdValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$c2 = $builder->addController('c2');
	$c2->setId('foo');
	$c2m2 = $c2->addMethod('method');
	$c2m2->setId('bar2');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: duplicate
Toolkit::test(function (): void {
	$validation = new IdValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar');

	$c2 = $builder->addController('c2');
	$c2->setId('foo');
	$c2m2 = $c2->addMethod('method');
	$c2m2->setId('bar');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, 'Duplicate #[Id] "foo.bar" in "c2::method()" and "c1::method()"');
});

// Validate: invalid characters
Toolkit::test(function (): void {
	$validation = new IdValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('{$bar}');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '#[Id] "{$bar}" in "c1::method()" contains illegal characters "{$". Allowed characters are only [a-zA-Z0-9_].');
});
