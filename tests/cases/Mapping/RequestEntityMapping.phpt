<?php declare(strict_types = 1);

/**
 * Test: Mapping\RequestEntityMapping
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\RequestEntityMapping;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\Schema\EndpointRequestBody;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use GuzzleHttp\Psr7\Utils;
use Tester\Assert;
use Tests\Fixtures\Mapping\Request\FooEntity;
use Tests\Fixtures\Mapping\Request\NotEmptyEntity;

// Add entity to request
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$mapping = new RequestEntityMapping();

	$handler = new EndpointHandler('class', 'method');
	$endpoint = new Endpoint($handler);
	$requestBody = new EndpointRequestBody();
	$requestBody->setEntity(FooEntity::class);
	$endpoint->setRequestBody($requestBody);

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	Assert::equal(
		$request->withAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, new FooEntity()),
		$mapping->map($request, $response)
	);
});

// Don't modify request by entity - method foo is not supported
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$mapping = new RequestEntityMapping();

	$handler = new EndpointHandler('class', 'method');
	$endpoint = new Endpoint($handler);
	$requestBody = new EndpointRequestBody();
	$requestBody->setEntity(FooEntity::class);
	$endpoint->setRequestBody($requestBody);

	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);
	$request = $request->withMethod('foo');

	Assert::same($request, $mapping->map($request, $response));
});

// No request mapper, return request
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$mapping = new RequestEntityMapping();

	$handler = new EndpointHandler('class', 'method');
	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, new Endpoint($handler));

	Assert::same($request, $mapping->map($request, $response));
});

// Exception - missing attribute
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$mapping = new RequestEntityMapping();

	Assert::exception(function () use ($mapping, $request, $response): void {
		$request = $mapping->map($request, $response);
	}, InvalidStateException::class, sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
});

// Mapping from query or body
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$entity = new NotEmptyEntity();

	$queryRequest = $request
		->withQueryParams(['foo' => 1]);

	$bodyRequest = $request
		->withBody(Utils::streamFor(json_encode(['foo' => 1])));

	foreach ([Endpoint::METHOD_GET, Endpoint::METHOD_DELETE] as $method) {
		$entity = $entity->fromRequest($queryRequest->withMethod($method));

		Assert::same(1, $entity->foo);
	}

	foreach ([Endpoint::METHOD_POST, Endpoint::METHOD_PUT, Endpoint::METHOD_PATCH] as $method) {
		$entity = $entity->fromRequest($bodyRequest->withMethod($method));

		Assert::same(1, $entity->foo);
	}
});
