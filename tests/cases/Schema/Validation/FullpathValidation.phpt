<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\FullpathValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Validation\FullpathValidation;
use Tester\Assert;

// Validate: success
test(function (): void {
	$validation = new FullpathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('foo1');
	$c1->setPath('foo2');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setPath('foo3');
	$c1m1->addHttpMethod(Endpoint::METHOD_GET);
	$c1m1->addHttpMethod(Endpoint::METHOD_POST);
	$c1m1->addHttpMethod(Endpoint::METHOD_PUT);

	$c2 = $builder->addController('c2');
	$c2->addGroupPath('bar1');
	$c2->setPath('bar2');
	$c2m2 = $c2->addMethod('method');
	$c2m2->setPath('bar3');
	$c2m2->addHttpMethod(Endpoint::METHOD_GET);
	$c2m2->addHttpMethod(Endpoint::METHOD_POST);
	$c2m2->addHttpMethod(Endpoint::METHOD_PUT);

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Validate: duplicate
test(function (): void {
	$validation = new FullpathValidation();
	$builder = new SchemaBuilder();

	$c1 = $builder->addController('c1');
	$c1->addGroupPath('foo1');
	$c1->setPath('foo2');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setPath('foo3');
	$c1m1->addHttpMethod(Endpoint::METHOD_GET);
	$c1m1->addHttpMethod(Endpoint::METHOD_POST);
	$c1m1->addHttpMethod(Endpoint::METHOD_PUT);

	$c2 = $builder->addController('c2');
	$c2->addGroupPath('foo1');
	$c2->setPath('foo2');
	$c2m2 = $c2->addMethod('method');
	$c2m2->setPath('foo3');
	$c2m2->addHttpMethod(Endpoint::METHOD_GET);
	$c2m2->addHttpMethod(Endpoint::METHOD_POST);
	$c2m2->addHttpMethod(Endpoint::METHOD_PUT);

	Assert::exception(function () use ($validation, $builder): void {
		$validation->validate($builder);
	}, InvalidSchemaException::class, 'Duplicate path "/foo1/foo2/foo3" in "c2::method()" and "c1::method()"');
});
