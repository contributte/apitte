<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\ControllerPathValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\ControllerPathValidation;
use Tester\Assert;

// Validate: success
test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/foo');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: no path
test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$builder->addController('c1');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@ControllerPath in "c1" must be set.');
});

// Validate: start with /
test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('foo');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@ControllerPath "foo" in "c1" must starts with "/" (slash).');
});

// Validate: end with /
test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/foo/');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@ControllerPath "/foo/" in "c1" must not ends with "/" (slash).');
});

// Validate: invalid characters
test(function (): void {
	$validation = new ControllerPathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->setPath('/{foo$}');

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, '@ControllerPath "/{foo$}" in "c1" contains illegal characters "{". Allowed characters are only [a-zA-Z0-9-_/].');
});
