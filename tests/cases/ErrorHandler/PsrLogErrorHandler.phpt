<?php declare(strict_types = 1);

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\ErrorHandler\PsrLogErrorHandler;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Psr\Log\Test\TestLogger;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Log - generic exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$error = new Exception('test');
	$handler->handle(new DispatchError($error, $request));

	Assert::same(
		[
			[
				'level' => 'error',
				'message' => 'test',
				'context' => [
					'exception' => $error,
				],
			],
		],
		$logger->records
	);
});

// Log - api exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$handler->handle(new DispatchError(
		new ClientErrorException('test', ApiResponse::S404_NOT_FOUND, null, ['foo' => 'bar']),
		$request
	));

	Assert::same([], $logger->records);
});

// Log - api exception with previous exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$previousError = new Exception('test');

	$clientError = new ClientErrorException('client', 400, $previousError);
	$handler->handle(new DispatchError($clientError, $request));

	$serverError = new ServerErrorException('server', 500, $previousError);
	$handler->handle(new DispatchError($serverError, $request));

	Assert::same(
		[
			[
				'level' => 'debug',
				'message' => 'test',
				'context' => [
					'exception' => $previousError,
				],
			],
			[
				'level' => 'error',
				'message' => 'test',
				'context' => [
					'exception' => $previousError,
				],
			],
		],
		$logger->records
	);
});

// Log - snapshot exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);

	// Api exception, without previous, not loggable
	$handler->handle(new DispatchError(
		new SnapshotException(
			new ClientErrorException('client'),
			$request,
			new ApiResponse(Psr7ResponseFactory::fromGlobal())
		),
		$request
	));

	$genericException = new Exception('generic');

	// Api exception, with previous, loggable
	$handler->handle(new DispatchError(
		new SnapshotException(
			new ClientErrorException('client', 400, $genericException),
			$request,
			new ApiResponse(Psr7ResponseFactory::fromGlobal())
		),
		$request
	));

	// Generic exception, loggable
	$handler->handle(new DispatchError(
		new SnapshotException(
			$genericException,
			$request,
			new ApiResponse(Psr7ResponseFactory::fromGlobal())
		),
		$request
	));

	Assert::same(
		[
			[
				'level' => 'debug',
				'message' => 'generic',
				'context' => [
					'exception' => $genericException,
				],
			],
			[
				'level' => 'error',
				'message' => 'generic',
				'context' => [
					'exception' => $genericException,
				],
			],
		],
		$logger->records
	);
});
