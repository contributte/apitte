<?php declare(strict_types = 1);

/**
 * Test: Dispatcher\DecoratedDispatcher
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Dispatcher\DecoratedDispatcher;
use Apitte\Core\ErrorHandling\JsonErrorConverter;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tests\Fixtures\Decorator\EarlyReturnResponseExceptionDecorator;
use Tests\Fixtures\ErrorHandling\RethrowErrorConverter;
use Tests\Fixtures\Handler\ErroneousHandler;
use Tests\Fixtures\Handler\FakeNullHandler;
use Tests\Fixtures\Handler\FakeResponseHandler;
use Tests\Fixtures\Handler\ReturnFooBarHandler;
use Tests\Fixtures\Router\FakeRouter;

//TODO - EarlyReturnResponseException
// 		- decorateRequest
//		- decorateResponse
//		- decorateError

// Match request, use handler and be happy, everything is ok!
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager(), new JsonErrorConverter());
	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, add endpoint, use handler and be happy, everything is ok!
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$handler = new EndpointHandler('class', 'method');
	$endpoint = new Endpoint($handler);
	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager(), new JsonErrorConverter());
	$response = $dispatcher->dispatch($request, $response);
	Assert::same($response->getAttribute(RequestAttributes::ATTR_ENDPOINT), $endpoint);
});

// Match request, use invalid handler, throw exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeNullHandler(), new DecoratorManager(), new JsonErrorConverter());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		try {
			$dispatcher->dispatch($request, $response);
		} catch (SnapshotException $exception) {
			throw $exception->getPrevious();
		} catch (Throwable $exception) {
			throw new InvalidArgumentException('This should never happen, try-catch is used just to get previous exception');
		}
	}, InvalidStateException::class, sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
});

// Match request, use invalid handler, throw exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ReturnFooBarHandler(), new DecoratorManager(), new JsonErrorConverter());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		try {
			$dispatcher->dispatch($request, $response);
		} catch (SnapshotException $exception) {
			throw $exception->getPrevious();
		} catch (Throwable $exception) {
			throw new InvalidArgumentException('This should never happen, try-catch is used just to get previous exception');
		}
	}, InvalidStateException::class, sprintf('If you want return anything else than "%s" from your api endpoint then install "apitte/negotiation".', ApiResponse::class));
});

// Match request, decorate request, throw exception, return response from exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager = new DecoratorManager();
	$manager->addRequestDecorator(new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager, new JsonErrorConverter());

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, decorate response, throw exception, return response from exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager = new DecoratorManager();
	$manager->addResponseDecorator(new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager, new JsonErrorConverter());

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, throw and catch exception, decorate response with exception in context and then (for tests) throw exception again
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), new DecoratorManager(), new RethrowErrorConverter());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, ServerErrorException::class, ServerErrorException::$defaultMessage);
});

// Match request, use handler, throw and catch exception then trow it again because DecoratorManager doesn't have any decorators so returned null
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), new DecoratorManager(), new JsonErrorConverter());
	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, RuntimeException::class, sprintf('I am %s!', ErroneousHandler::class));
});

// No match, throw exception
test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(false), new FakeResponseHandler(), new DecoratorManager(), new JsonErrorConverter());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		try {
			$dispatcher->dispatch($request, $response);
		} catch (SnapshotException $exception) {
			throw $exception->getPrevious();
		} catch (Throwable $exception) {
			throw new InvalidArgumentException('This should never happen, try-catch is used just to get previous exception');
		}
	}, ClientErrorException::class, 'No matched route by given URL');
});
