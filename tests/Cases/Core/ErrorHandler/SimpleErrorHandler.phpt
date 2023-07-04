<?php declare(strict_types = 1);

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Error conversion - api exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$handler = new SimpleErrorHandler();
	$response = $handler->handle(new DispatchError(
		new ClientErrorException('test', ApiResponse::S404_NOT_FOUND, null, ['foo' => 'bar']),
		$request
	));

	Assert::same(
		[
			'status' => 'error',
			'code' => 404,
			'message' => 'test',
			'context' => ['foo' => 'bar'],
		],
		$response->getJsonBody()
	);
});

// Error conversion - generic exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$handler = new SimpleErrorHandler();
	$response = $handler->handle(new DispatchError(new Exception('test', 400), $request));

	Assert::same(
		[
			'status' => 'error',
			'code' => 500,
			'message' => ServerErrorException::$defaultMessage,
		],
		$response->getJsonBody()
	);
});

// Snapshot
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$handler = new SimpleErrorHandler();
	$originalResponse = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$response = $handler->handle(new DispatchError(
		new SnapshotException(
			new ClientErrorException('test'),
			new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
			$originalResponse
		),
		$request
	));

	Assert::same($originalResponse, $response);
});

// Exception catching disabled
Toolkit::test(function (): void {
	$handler = new SimpleErrorHandler();
	$handler->setCatchException(false);

	Assert::exception(function () use ($handler): void {
		$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
		$handler->handle(new DispatchError(new ClientErrorException('test'), $request));
	}, ClientErrorException::class, 'test');
});

// Exception catching disabled - snapshot
Toolkit::test(function (): void {
	$handler = new SimpleErrorHandler();
	$handler->setCatchException(false);

	Assert::exception(function () use ($handler): void {
		$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
		$handler->handle(new DispatchError(
			new SnapshotException(
				new ClientErrorException('test'),
				new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
				new ApiResponse(Psr7ResponseFactory::fromGlobal())
			),
			$request
		));
	}, ClientErrorException::class, 'test');
});
