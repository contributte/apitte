<?php declare(strict_types = 1);

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Middlewares\ApiMiddleware;
use Contributte\Middlewares\Utils\Lambda;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = Mockery::mock(IDispatcher::class);
	$dispatcher->shouldReceive('dispatch')
		->once()
		->with($request, $response)
		->andReturn($response);

	$middleware = new ApiMiddleware($dispatcher, new SimpleErrorHandler());
	$returned = $middleware($request, $response, Lambda::leaf());

	Assert::type($response, $returned);
	Assert::same($response, $returned);
});

Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$error = ClientErrorException::create()
		->withMessage('test');

	$dispatcher = Mockery::mock(IDispatcher::class);
	$dispatcher->shouldReceive('dispatch')
		->once()
		->with($request, $response)
		->andThrow($error);

	$middleware = new ApiMiddleware($dispatcher, new SimpleErrorHandler());
	$returned = $middleware($request, $response, Lambda::leaf());
	assert($returned instanceof ApiResponse);

	Assert::type($response, $returned);
	Assert::same(
		['status' => 'error', 'code' => 400, 'message' => 'test'],
		$returned->getJsonBody()
	);
});
