<?php declare(strict_types = 1);

/**
 * Test: Dispatcher\DecoratedDispatcher
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Decorator\IDecorator;
use Apitte\Core\Dispatcher\DecoratedDispatcher;
use Apitte\Core\Exception\Logical\BadRequestException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tests\Fixtures\Decorator\EarlyReturnResponseExceptionDecorator;
use Tests\Fixtures\Decorator\ReturnNullDecorator;
use Tests\Fixtures\Decorator\ThrowExceptionFromContextResponseDecorator;
use Tests\Fixtures\Handler\ErroneousHandler;
use Tests\Fixtures\Handler\FakeNullHandler;
use Tests\Fixtures\Handler\FakeResponseHandler;
use Tests\Fixtures\Handler\ReturnFooBarHandler;
use Tests\Fixtures\Router\FakeRouter;

//TODO - EarlyReturnResponseException
// 		- decorateRequest
//		- decorateResponse

// Match request, use handler and be happy, everything is ok!
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager());
	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, add endpoint, use handler and be happy, everything is ok!
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$handler = new EndpointHandler('class', 'method');
	$endpoint = new Endpoint($handler);
	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager());
	$response = $dispatcher->dispatch($request, $response);
	Assert::same($response->getAttribute(RequestAttributes::ATTR_ENDPOINT), $endpoint);
});

// Match request, use invalid handler, throw exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeNullHandler(), new DecoratorManager());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, InvalidStateException::class, sprintf('Handler returned response must implement "%s"', ResponseInterface::class));
});

// Match request, use invalid handler, throw exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ReturnFooBarHandler(), new DecoratorManager());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, InvalidStateException::class, sprintf('If you want return anything else than "%s" from your api endpoint then install "apitte/negotiation".', ResponseInterface::class));
});

// Match request, decorate request, throw exception, return response from exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager = new DecoratorManager();
	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager);

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, decorate response, throw exception, return response from exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager = new DecoratorManager();
	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager);

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, throw and catch exception, decorate response with exception in context and then (for tests) throw exception again
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager = new DecoratorManager();
	$manager->addDecorator(IDecorator::ON_DISPATCHER_EXCEPTION, new ThrowExceptionFromContextResponseDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), $manager);

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, RuntimeException::class, sprintf('I am %s!', ErroneousHandler::class));
});

// Match request, use handler, throw and catch exception then trow it again because DecoratorManager doesn't have any decorators so returned null
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), new DecoratorManager());
	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, RuntimeException::class, sprintf('I am %s!', ErroneousHandler::class));
});

// Match request, use handler, throw and catch exception then throw it again because decorator returned null response
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager = new DecoratorManager();
	$manager->addDecorator(IDecorator::ON_DISPATCHER_EXCEPTION, new ReturnNullDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), $manager);

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, RuntimeException::class, sprintf('I am %s!', ErroneousHandler::class));
});

// No match, throw exception
test(function (): void {
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$dispatcher = new DecoratedDispatcher(new FakeRouter(false), new FakeResponseHandler(), new DecoratorManager());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, BadRequestException::class, 'No matched route by given URL');
});
