<?php declare(strict_types = 1);

require_once __DIR__ . '/../../../bootstrap.php';

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\Decorator\ReturnRequestDecorator;
use Tests\Fixtures\Decorator\ReturnResponseDecorator;

// Decorate request
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager->addRequestDecorator(new ReturnRequestDecorator());
	$manager->addRequestDecorator(new ReturnRequestDecorator());

	Assert::same($request, $manager->decorateRequest($request, $response));
});

// Decorate request - no decorators
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	Assert::same($request, $manager->decorateRequest($request, $response));
});

// Decorate response
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$manager->addResponseDecorator(new ReturnResponseDecorator());
	$manager->addResponseDecorator(new ReturnResponseDecorator());

	Assert::same($response, $manager->decorateResponse($request, $response));
});

// Decorate response - no decorators
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	Assert::same($response, $manager->decorateResponse($request, $response));
});

// Decorate error
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$error = new Exception('I am a generic exception');
	$error = ServerErrorException::create()->withPrevious($error);

	$manager->addErrorDecorator(new ReturnResponseDecorator());
	$manager->addErrorDecorator(new ReturnResponseDecorator());

	Assert::same($response, $manager->decorateError($request, $response, $error));
});

// Decorate error - no decorators
Toolkit::test(function (): void {
	$manager = new DecoratorManager();
	$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());
	$error = new Exception('I am a generic exception');
	$error = ServerErrorException::create()->withPrevious($error);

	Assert::same(null, $manager->decorateError($request, $response, $error));
});
