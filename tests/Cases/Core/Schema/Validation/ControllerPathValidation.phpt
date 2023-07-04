<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\ControllerPathValidation;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Validate: success
Toolkit::test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/foo');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: no path
Toolkit::test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$builder->addController('c1');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path in "c1" must be set.');
});

// Validate: start with /
Toolkit::test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('foo');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "foo" in "c1" must starts with "/" (slash).');
});

// Validate: end with /
Toolkit::test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/foo/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/foo/" in "c1" must not ends with "/" (slash).');
});

// Validate: invalid characters
Toolkit::test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/{foo$}');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@Path "/{foo$}" in "c1" contains illegal characters "{". Allowed characters are only [a-zA-Z0-9-_/].');
});
