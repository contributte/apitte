<?php declare(strict_types = 1);

/**
 * Test: Dispatcher\WrappedDispatcher
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Dispatcher\WrappedDispatcher;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;
use Tests\Fixtures\Dispatcher\FakeDispatcher;

test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new WrappedDispatcher(new FakeDispatcher(), new SimpleErrorHandler());

	Assert::same($response, $dispatcher->dispatch($request, $response));
});
