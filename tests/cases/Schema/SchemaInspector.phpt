<?php declare(strict_types = 1);

/**
 * Test: Schema\SchemaInspector
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Schema\SchemaInspector;
use Tester\Assert;

// GetEndpointsByTag: empty
test(function (): void {
	$schema = new Schema();

	$e1 = new Endpoint();
	$e1->addTag('bar', 'bar1');
	$schema->addEndpoint($e1);

	$inspector = new SchemaInspector($schema);

	Assert::same([], $inspector->getEndpointsByTag('foo'));
});

// GetEndpointsByTag: by name
test(function (): void {
	$schema = new Schema();

	$e1 = new Endpoint();
	$e1->addTag('foo', 'foo1');
	$schema->addEndpoint($e1);

	$e2 = new Endpoint();
	$e2->addTag('foo', 'foo2');
	$schema->addEndpoint($e2);

	$e3 = new Endpoint();
	$e3->addTag('foo', 'foo3');
	$schema->addEndpoint($e3);

	$e4 = new Endpoint();
	$e4->addTag('bar', 'bar1');
	$schema->addEndpoint($e4);

	$inspector = new SchemaInspector($schema);

	Assert::same([$e1, $e2, $e3], $inspector->getEndpointsByTag('foo'));
});

// GetEndpointsByTag: by name and value
test(function (): void {
	$schema = new Schema();

	$e1 = new Endpoint();
	$e1->addTag('foo', 'foo1');
	$schema->addEndpoint($e1);

	$e2 = new Endpoint();
	$e2->addTag('foo', 'foo2');
	$schema->addEndpoint($e2);

	$e3 = new Endpoint();
	$e3->addTag('foo', 'foo3');
	$schema->addEndpoint($e3);

	$e4 = new Endpoint();
	$e4->addTag('bar', 'bar1');
	$schema->addEndpoint($e4);

	$inspector = new SchemaInspector($schema);

	Assert::same([$e1], $inspector->getEndpointsByTag('foo', 'foo1'));
});
