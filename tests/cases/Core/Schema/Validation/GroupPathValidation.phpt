<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\GroupPathValidation
 */

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\GroupPathValidation;
use Tester\Assert;

// Validate: success
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: success with parameter
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo/{bar}');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: only /
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/" in "c1" cannot be only "/", it is nonsense.');
});

// Validate: starts with /
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('foo');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "foo" in "c1" must starts with "/" (slash).');
});

// Validate: end with /
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foo/" in "c1" must not ends with "/" (slash).');
});

// Validate: invalid characters
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo$');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foo$" in "c1" contains illegal characters "$". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (contains)
test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/{foo&&&bar}');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{foo&&&bar}" in "c1" contains illegal characters "&&&". Allowed characters are only [a-zA-Z0-9-_/{}].');
});
