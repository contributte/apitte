<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\PathValidation;
use Contributte\Tester\Toolkit;
use Tester\Assert;

// Validate: start slash
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('foobar');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "foobar" in "c1::foo()" must starts with "/" (slash).');
});

// Validate: end slash
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('/foobar/');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "/foobar/" in "c1::foo()" must not ends with "/" (slash).');
});

// Validate: empty path
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('');
	$c1m1->addHttpMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '"c1::foo1()" has empty #[Path].');
});

// Validate: invalid parameter (ends)
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo$}');
	$c1m1->addHttpMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "/{foo$}" in "c1::foo1()" contains illegal characters "$". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (starts)
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{%foo}');
	$c1m1->addHttpMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "/{%foo}" in "c1::foo1()" contains illegal characters "%". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (contains)
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo&&&bar}');
	$c1m1->addHttpMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "/{foo&&&bar}" in "c1::foo1()" contains illegal characters "&&&". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: parameter containing slash (allowed in path, disallowed in parameter)
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo/bar}');
	$c1m1->addHttpMethod('GET');

	Assert::exception(function () use ($builder): void {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, InvalidSchemaException::class, '#[Path] "/{foo/bar}" in "c1::foo1()" contains illegal characters "/" in parameter. Allowed characters in parameter are only {[a-z-A-Z0-9-_]+}');
});

// Validate: multiple parameters
Toolkit::test(function (): void {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo}/{bar}');
	$c1m1->addHttpMethod('GET');

	try {
		$validator = new PathValidation();
		$validator->validate($builder);
	} catch (Throwable) {
		Assert::fail('This is fail. Parameters are OK.');
	}
});
