<?php declare(strict_types = 1);

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
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$error = new Exception('test');
	$handler->handle($error);

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
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$handler->handle(new ClientErrorException('test', ApiResponse::S404_NOT_FOUND, null, ['foo' => 'bar']));

	Assert::same([], $logger->records);
});

// Log - api exception with previous exception
test(function (): void {
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$previousError = new Exception('test');

	$clientError = new ClientErrorException('client', 400, $previousError);
	$handler->handle($clientError);

	$serverError = new ServerErrorException('server', 500, $previousError);
	$handler->handle($serverError);

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
	$logger = new TestLogger();
	$handler = new PsrLogErrorHandler($logger);
	$handler->handle(new SnapshotException(
		new ClientErrorException('test'),
		new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal()),
		new ApiResponse(Psr7ResponseFactory::fromGlobal())
	));

	Assert::same([], $logger->records);
});
