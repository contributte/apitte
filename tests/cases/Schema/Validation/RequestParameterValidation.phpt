<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\RequestParameterValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\Builder\SchemaBuilder;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Validation\RequestParameterValidation;
use Tester\Assert;

// Not defined, no error
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new RequestParameterValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// No error
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new RequestParameterValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$c1m1->addParameter('foo', EndpointParameter::TYPE_STRING);

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Invalid type
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new RequestParameterValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$c1m1->addParameter('foo', 'bar');

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Invalid request parameter "type=%s" given in "%s::%s()". Choose one of %s::TYPE_*',
			'bar',
			'c1',
			'method',
			EndpointParameter::class
		)
	);
});

// Invalid in
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new RequestParameterValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$p1 = $c1m1->addParameter('foo');
	$p1->setIn('bar');

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Invalid request parameter "in=%s" given in "%s::%s()". Choose one of %s::IN_*',
			'bar',
			'c1',
			'method',
			EndpointParameter::class
		)
	);
});
