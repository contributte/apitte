<?php declare(strict_types = 1);

/**
 * Test: Decorator\DecoratorManager
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Decorator\IDecorator;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;
use Tests\Fixtures\Decorator\ReturnNullDecorator;
use Tests\Fixtures\Decorator\ReturnRequestDecorator;
use Tests\Fixtures\Decorator\ReturnResponseDecorator;

// Decorate request
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnRequestDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnRequestDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnRequestDecorator());

	Assert::same($request, $manager->decorateRequest(IDecorator::ON_HANDLER_BEFORE, $request, $response));
});

// Decorate request - return null
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnRequestDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnNullDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_BEFORE, new ReturnRequestDecorator());

	Assert::same(null, $manager->decorateRequest(IDecorator::ON_HANDLER_BEFORE, $request, $response));
});

// Decorate request - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	Assert::same($request, $manager->decorateRequest(IDecorator::ON_HANDLER_BEFORE, $request, $response));
});

// Decorate response
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnResponseDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnResponseDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnResponseDecorator());

	Assert::same($response, $manager->decorateResponse(IDecorator::ON_HANDLER_AFTER, $request, $response));
});

// Decorate response - return null
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnResponseDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnNullDecorator());
	$manager->addDecorator(IDecorator::ON_HANDLER_AFTER, new ReturnResponseDecorator());

	Assert::same(null, $manager->decorateResponse(IDecorator::ON_HANDLER_AFTER, $request, $response));
});

// Decorate response - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	Assert::same($response, $manager->decorateResponse(IDecorator::ON_HANDLER_AFTER, $request, $response));
});

// Decorate response - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	Assert::same($response, $manager->decorateResponse(IDecorator::ON_HANDLER_AFTER, $request, $response));
});

// Decorate response exception - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = Psr7ServerRequestFactory::fromSuperGlobal();
	$response = Psr7ResponseFactory::fromGlobal();

	Assert::same(null, $manager->decorateResponse(IDecorator::ON_DISPATCHER_EXCEPTION, $request, $response));
});
