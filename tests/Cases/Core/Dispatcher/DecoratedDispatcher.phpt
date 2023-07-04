<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Dispatcher\DecoratedDispatcher;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tests\Fixtures\Decorator\EarlyReturnResponseExceptionDecorator;
use Tests\Fixtures\Decorator\RethrowErrorDecorator;
use Tests\Fixtures\Handler\ErroneousHandler;
use Tests\Fixtures\Handler\FakeNullHandler;
use Tests\Fixtures\Handler\FakeResponseHandler;
use Tests\Fixtures\Router\FakeRouter;

//TODO - EarlyReturnResponseException
// 		- decorateRequest
//		- decorateResponse
//		- decorateError

// Match request, use handler and be happy, everything is ok!
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager());
	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, add endpoint, use handler and be happy, everything is ok!
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$handler = new EndpointHandler('class', 'method');
	$endpoint = new Endpoint($handler);
	$request = $request->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), new DecoratorManager());
	$response = $dispatcher->dispatch($request, $response);
	Assert::same($response->getAttribute(RequestAttributes::ATTR_ENDPOINT), $endpoint);
});

// Match request, use invalid handler, throw exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeNullHandler(), new DecoratorManager());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$dispatcher->dispatch($request, $response);
	}, InvalidStateException::class, sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
});

// Match request, decorate request, throw exception, return response from exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager = new DecoratorManager();
	$manager->addRequestDecorator(new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager);

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, decorate response, throw exception, return response from exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager = new DecoratorManager();
	$manager->addResponseDecorator(new EarlyReturnResponseExceptionDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new FakeResponseHandler(), $manager);

	Assert::same($response, $dispatcher->dispatch($request, $response));
});

// Match request, use handler, throw and catch exception, decorate response with exception in context and then (for tests) throw exception again
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager = new DecoratorManager();
	$manager->addErrorDecorator(new RethrowErrorDecorator());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), $manager);

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, ServerErrorException::class, ServerErrorException::$defaultMessage);
});

// Match request, use handler, throw and catch exception then trow it again because DecoratorManager doesn't have any decorators so returned null
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(true), new ErroneousHandler(), new DecoratorManager());
	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, RuntimeException::class, sprintf('I am %s!', ErroneousHandler::class));
});

// No match, throw exception
Toolkit::test(function (): void {
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$dispatcher = new DecoratedDispatcher(new FakeRouter(false), new FakeResponseHandler(), new DecoratorManager());

	Assert::exception(function () use ($dispatcher, $request, $response): void {
		$response = $dispatcher->dispatch($request, $response);
	}, ClientErrorException::class, 'No matched route by given URL');
});
