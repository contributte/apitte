<?php declare(strict_types = 1);

/**
 * Test: Mapping\RequestEntityMapping
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Request\IRequestEntity;
use Apitte\Core\Mapping\RequestEntityMapping;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointRequestMapper;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;
use Tests\Fixtures\Mapping\Request\InvalidEntity;

// Add entity to request
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = Psr7ResponseFactory::fromGlobal();
	$mapping = new RequestEntityMapping();

	$endpoint = new Endpoint();
	$mapper = new EndpointRequestMapper();
	$mapper->setEntity(FooEntity::class);
	$endpoint->setRequestMapper($mapper);

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	Assert::equal(
		$request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, new FooEntity()),
		$mapping->map($request, $response)
	);
});

// Don't modify request by entity - method foo is not supported
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = Psr7ResponseFactory::fromGlobal();
	$mapping = new RequestEntityMapping();

	$endpoint = new Endpoint();
	$mapper = new EndpointRequestMapper();
	$mapper->setEntity(FooEntity::class);
	$endpoint->setRequestMapper($mapper);

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);
	$request = $request->withMethod('foo');

	Assert::same($request, $mapping->map($request, $response));
});

// Exception - invalid entity
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();
	$mapping = new RequestEntityMapping();

	$endpoint = new Endpoint();
	$mapper = new EndpointRequestMapper();
	$mapper->setEntity(InvalidEntity::class);
	$endpoint->setRequestMapper($mapper);

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	Assert::exception(function () use ($mapping, $request, $response): void {
		$request = $mapping->map($request, $response);
	}, InvalidStateException::class, sprintf('Instantiated entity "%s" does not implement "%s"', InvalidEntity::class, IRequestEntity::class));
});

// No request mapper, return request
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();
	$mapping = new RequestEntityMapping();

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, new Endpoint());

	Assert::same($request, $mapping->map($request, $response));
});

// Exception - missing attribute
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();
	$mapping = new RequestEntityMapping();

	Assert::exception(function () use ($mapping, $request, $response): void {
		$request = $mapping->map($request, $response);
	}, InvalidStateException::class, sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
});
