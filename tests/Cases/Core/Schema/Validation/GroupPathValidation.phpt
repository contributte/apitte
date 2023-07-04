<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\GroupPathValidation;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Validate: success
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: success with parameter
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo/{bar}');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: only /
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/" in "c1" cannot be only "/", it is nonsense.');
});

// Validate: starts with /
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('foo');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "foo" in "c1" must starts with "/" (slash).');
});

// Validate: end with /
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foo/" in "c1" must not ends with "/" (slash).');
});

// Validate: invalid characters
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/foo$');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foo$" in "c1" contains illegal characters "$". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (contains)
Toolkit::test(function (): void {
	$validation = new GroupPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('/{foo&&&bar}');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{foo&&&bar}" in "c1" contains illegal characters "&&&". Allowed characters are only [a-zA-Z0-9-_/{}].');
});
