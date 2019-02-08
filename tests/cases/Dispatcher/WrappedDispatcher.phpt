<?php declare(strict_types = 1);

/**
 * Test: Dispatcher\WrappedDispatcher
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Dispatcher\WrappedDispatcher;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;
use Tests\Fixtures\Dispatcher\FakeDispatcher;
use Tests\Fixtures\Utils\FakeLogger;

test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new WrappedDispatcher(new FakeDispatcher(), new FakeLogger());

	Assert::same($response, $dispatcher->dispatch($request, $response));
});
