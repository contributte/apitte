<?php declare(strict_types = 1);

use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Error conversion - api exception
test(function (): void {
	$handler = new SimpleErrorHandler();
	$response = $handler->handle(new ClientErrorException('test', ApiResponse::S404_NOT_FOUND, null, ['foo' => 'bar']));

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
test(function (): void {
	$handler = new SimpleErrorHandler();
	$response = $handler->handle(new Exception('test', 400));

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
test(function (): void {
	$handler = new SimpleErrorHandler();
	$originalResponse = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$response = $handler->handle(new SnapshotException(
		new ClientErrorException('test'),
		new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
		$originalResponse
	));

	Assert::same($originalResponse, $response);
});

// Exception catching disabled
test(function (): void {
	$handler = new SimpleErrorHandler();
	$handler->setCatchException(false);

	Assert::exception(function () use ($handler): void {
		$handler->handle(new ClientErrorException('test'));
	}, ClientErrorException::class, 'test');
});

// Exception catching disabled - snapshot
test(function (): void {
	$handler = new SimpleErrorHandler();
	$handler->setCatchException(false);

	Assert::exception(function () use ($handler): void {
		$handler->handle(new SnapshotException(
			new ClientErrorException('test'),
			new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
			new ApiResponse(Psr7ResponseFactory::fromGlobal())
		));
	}, ClientErrorException::class, 'test');
});
