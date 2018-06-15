<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\PathValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\PathValidation;
use Tester\Assert;

// Validate: start slash
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('foobar');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '@Path "foobar" in "c1::foo()" must starts with "/" (slash).');
});

// Validate: end slash
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('/foobar/');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foobar/" in "c1::foo()" must not ends with "/" (slash).');
});

// Validate: invalid parameter (ends)
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo$}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{foo$}" in "c1::foo1()" contains illegal characters "$". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (starts)
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{%foo}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{%foo}" in "c1::foo1()" contains illegal characters "%". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (contains)
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo&&&bar}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{foo&&&bar}" in "c1::foo1()" contains illegal characters "&&&". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: multiple parameters
test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo}/{bar}');
	$c1m1->addMethod('GET');
	try {
		$validator = new PathValidation();
		$validator->validate($builder);
	} catch (Throwable $e) {
		Assert::fail('This is fail. Parameters are OK.');
	}
});
