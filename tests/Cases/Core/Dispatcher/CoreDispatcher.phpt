<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Dispatcher\CoreDispatcher;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tests\Fixtures\Handler\FakeNullHandler;
use Tests\Fixtures\Handler\FakeResponseHandler;
use Tests\Fixtures\Router\FakeRouter;

// Request matched, use handler, return response
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new CoreDispatcher(new FakeRouter(true), new FakeResponseHandler());
	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Request matched, use invalid handler, throw exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new CoreDispatcher(new FakeRouter(true), new FakeNullHandler());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, InvalidStateException::class, sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
});

// Request not matched, throw exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new CoreDispatcher(new FakeRouter(false), new FakeResponseHandler());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, ClientErrorException::class, 'No matched route by given URL');
});
