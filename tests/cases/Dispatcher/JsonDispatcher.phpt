<?php declare(strict_types = 1);

/**
 * Test: Dispatcher\JsonDispatcher
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Dispatcher\JsonDispatcher;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tests\Fixtures\Handler\FakeNullHandler;
use Tests\Fixtures\Handler\FakeResponseHandler;
use Tests\Fixtures\Handler\ReturnFooBarHandler;
use Tests\Fixtures\Router\FakeRouter;

// Matched, use handle
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new JsonDispatcher(new FakeRouter(true), new FakeResponseHandler());
	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Matched, use handle, write to response body result from handle
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new JsonDispatcher(new FakeRouter(true), new ReturnFooBarHandler());
	$response = $dispatcher->dispatch($request, $response);

	Assert::same(200, $response->getStatusCode());
	Assert::same(Json::encode(['foo', 'bar']), (string) $response->getBody());
});

// Matched, use invalid handle, throw exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new JsonDispatcher(new FakeRouter(true), new FakeNullHandler());
	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, InvalidStateException::class, sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
});

// Not matched, use fallback, write error to response body
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new JsonDispatcher(new FakeRouter(false), new FakeResponseHandler());
	$response = $dispatcher->dispatch($request, $response);

	Assert::same(404, $response->getStatusCode());
	Assert::same(['application/json'], $response->getHeader('Content-Type'));
	Assert::same(Json::encode(['error' => 'No matched route by given URL']), (string) $response->getBody());
});
