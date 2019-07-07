<?php declare(strict_types = 1);

/**
 * Test: Decorator\DecoratorManager
 */

require_once __DIR__ . '/../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Tester\Assert;
use Tests\Fixtures\Decorator\ReturnRequestDecorator;
use Tests\Fixtures\Decorator\ReturnResponseDecorator;

// Decorate request
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager->addRequestDecorator(new ReturnRequestDecorator());
	$manager->addRequestDecorator(new ReturnRequestDecorator());

	Assert::same($request, $manager->decorateRequest($request, $response));
});

// Decorate request - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	Assert::same($request, $manager->decorateRequest($request, $response));
});

// Decorate response
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager->addResponseDecorator(new ReturnResponseDecorator());
	$manager->addResponseDecorator(new ReturnResponseDecorator());

	Assert::same($response, $manager->decorateResponse($request, $response));
});

// Decorate response - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	Assert::same($response, $manager->decorateResponse($request, $response));
});

// Decorate error
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$error = new ServerErrorException('I am a generic exception');

	$manager->addErrorDecorator(new ReturnResponseDecorator());
	$manager->addErrorDecorator(new ReturnResponseDecorator());

	Assert::same($response, $manager->decorateError($request, $response, $error));
});

// Decorate error - no decorators
test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$error = new ServerErrorException('I am a generic exception');

	Assert::same(null, $manager->decorateError($request, $response, $error));
});
