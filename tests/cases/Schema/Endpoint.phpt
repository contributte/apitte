<?php declare(strict_types = 1);

/**
 * Test: Schema\Endpoint
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointParameter;
use Tester\Assert;

// AddMethod: success
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	$endpoint->addMethod($endpoint::METHOD_GET);
	$endpoint->addMethod($endpoint::METHOD_POST);

	Assert::same([$endpoint::METHOD_GET, $endpoint::METHOD_POST], $endpoint->getMethods());
});

// AddMethod: fail
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	Assert::exception(function () use ($endpoint): void {
		$endpoint->addMethod('foo');
	}, InvalidArgumentException::class, 'Method FOO is not allowed');

	Assert::exception(function () use ($endpoint): void {
		$endpoint->addMethod('FOO');
	}, InvalidArgumentException::class, 'Method FOO is not allowed');
});

// HasMethod: success
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	$endpoint->addMethod($endpoint::METHOD_GET);

	Assert::true($endpoint->hasMethod('get'));
	Assert::true($endpoint->hasMethod('GET'));
});

// HasMethod: fail
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	Assert::false($endpoint->hasMethod('foo'));
});

// GetPattern: fail, empty
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	Assert::exception(function () use ($endpoint): void {
		$endpoint->getPattern();
	}, InvalidStateException::class, 'Pattern attribute is required');
});

// GetParametersByIn: empty
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	Assert::same([], $endpoint->getParametersByIn('foo'));
});

// GetParametersByIn: success, in cookie
test(function (): void {
	$handler = new EndpointHandler('class', 'method');

	$endpoint = new Endpoint($handler);

	$p1 = new EndpointParameter('p1');
	$p1->setIn(EndpointParameter::IN_COOKIE);
	$endpoint->addParameter($p1);

	$p2 = new EndpointParameter('p2');
	$p2->setIn(EndpointParameter::IN_COOKIE);
	$endpoint->addParameter($p2);

	$p3 = new EndpointParameter('p3');
	$p3->setIn(EndpointParameter::IN_PATH);
	$endpoint->addParameter($p3);

	Assert::same(['p1' => $p1, 'p2' => $p2], $endpoint->getParametersByIn(EndpointParameter::IN_COOKIE));
});
