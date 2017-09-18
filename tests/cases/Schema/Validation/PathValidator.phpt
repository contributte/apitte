<?php

/**
 * Test: Schema\Validation\PathValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\ValidationException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Validation\PathValidation;
use Tester\Assert;

// Validate: start slash
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('foobar');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, '@Path "foobar" in "c1::foo()" must starts with "/" (slash).');
});

// Validate: end slash
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo');
	$c1m1->setPath('/foobar/');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, '@Path "/foobar/" in "c1::foo()" must not ends with "/" (slash).');
});

// Validate: duplicities
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/foobar');
	$c1m1->addMethod('GET');

	$c1m2 = $c1->addMethod('foo2');
	$c1m2->setPath('/foobar');
	$c1m2->addMethod('GET');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, 'Duplicate @Path "/foobar" in c1 at methods "foo2()" and "foo1()"');
});

// Validate: duplicities
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/foobar');
	$c1m1->setMethods(['GET', 'POST']);

	$c1m2 = $c1->addMethod('foo2');
	$c1m2->setPath('/foobar');
	$c1m2->setMethods(['POST', 'PUT']);

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, 'Duplicate @Path "/foobar" in c1 at methods "foo2()" and "foo1()"');
});

// Validate: [NOT] duplicities
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/foobar');
	$c1m1->addMethod('GET');

	$c1m2 = $c1->addMethod('foo2');
	$c1m2->setPath('/foobar');
	$c1m2->setMethods(['POST']);

	try {
		$validator = new PathValidation();
		$validator->validate($builder);
	} catch (Exception $e) {
		Assert::fail('This is fail. Paths+Method are different.');
	}
});

// Validate: invalid parameter (ends)
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo$}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, '@Path "/{foo$}" in "c1::foo1()" contains illegal characters "$". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (starts)
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{%foo}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, '@Path "/{%foo}" in "c1::foo1()" contains illegal characters "%". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: invalid parameter (contains)
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo&&&bar}');
	$c1m1->addMethod('GET');

	Assert::exception(function () use ($builder) {
		$validator = new PathValidation();
		$validator->validate($builder);
	}, ValidationException::class, '@Path "/{foo&&&bar}" in "c1::foo1()" contains illegal characters "&&&". Allowed characters are only [a-zA-Z0-9-_/{}].');
});

// Validate: multiple parameters
test(function () {
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1m1 = $c1->addMethod('foo1');
	$c1m1->setPath('/{foo}/{bar}');
	$c1m1->addMethod('GET');
	try {
		$validator = new PathValidation();
		$validator->validate($builder);
	} catch (Exception $e) {
		Assert::fail('This is fail. Parameters are OK.');
	}
});
