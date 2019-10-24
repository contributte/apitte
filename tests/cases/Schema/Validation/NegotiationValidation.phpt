<?php declare(strict_types = 1);

/**
 * Test: Schema\Validation\NegotiationValidation
 */

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidSchemaException;
use Apitte\Core\Schema\SchemaBuilder;
use Apitte\Core\Schema\Validation\NegotiationValidation;
use Tester\Assert;
use Tests\Fixtures\Negotiation\FooRenderer;
use Tests\Fixtures\Negotiation\InvalidRenderer;

// Not defined, no error
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new NegotiationValidation();

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
	$validation = new NegotiationValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$n1 = $c1m1->addNegotiation('json');
	$n1->setRenderer(FooRenderer::class);
	$n1->setDefault(true);

	Assert::noError(function () use ($validation, $builder): void {
		$validation->validate($builder);
	});
});

// Renderer does not exist
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new NegotiationValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$n1 = $c1m1->addNegotiation('json');
	$n1->setRenderer('foo');
	$n1->setDefault(true);

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Negotiation renderer "%s" in "%s::%s()" does not exists',
			'foo',
			'c1',
			'method'
		)
	);
});

// Renderer does not exist
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new NegotiationValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$n1 = $c1m1->addNegotiation('json');
	$n1->setRenderer(InvalidRenderer::class);
	$n1->setDefault(true);

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Negotiation renderer "%s" in "%s::%s()" does not implement __invoke(ApiRequest $request, ApiResponse $response, array $context): ApiResponse',
			InvalidRenderer::class,
			'c1',
			'method'
		)
	);
});

// Multiple defaults
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new NegotiationValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$n1 = $c1m1->addNegotiation('json');
	$n1->setDefault(true);

	$n2 = $c1m1->addNegotiation('xml');
	$n2->setDefault(true);

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Multiple negotiations with "default=true" given in "%s::%s()". Only one negotiation could be default.',
			'c1',
			'method'
		)
	);
});

// Multiple negotiations with same suffix
test(function (): void {
	$builder = new SchemaBuilder();
	$validation = new NegotiationValidation();

	$c1 = $builder->addController('c1');
	$c1->setId('foo');
	$c1m1 = $c1->addMethod('method');
	$c1m1->setId('bar1');

	$c1m1->addNegotiation('json');
	$c1m1->addNegotiation('json');

	Assert::exception(
		function () use ($validation, $builder): void {
			$validation->validate($builder);
		},
		InvalidSchemaException::class,
		sprintf(
			'Multiple negotiations with "suffix=%s" given in "%s::%s()". Each negotiation must have unique suffix',
			'json',
			'c1',
			'method'
		)
	);
});
