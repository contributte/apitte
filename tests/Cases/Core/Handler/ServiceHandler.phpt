<?php declare(strict_types = 1);

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Handler\ServiceHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointHandler;
use Apitte\Core\UI\Controller\IController;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Tester\Toolkit;
use Nette\DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

// Missing endpoint
Toolkit::test(function (): void {
	$container = new Container();
	$sh = new ServiceHandler($container);

	Assert::exception(function () use ($sh): void {
		$request = new ApiRequest(Psr7ServerRequestFactory::fromSuperGlobal());
		$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

		$sh->handle($request, $response);
	}, InvalidStateException::class, 'Attribute "apitte.core.endpoint" is required');
});

// Missing endpoint
Toolkit::test(function (): void {
	$controller = new class() implements IController
	{

		/**
		 * @return mixed[]
		 */
		public function foobar(ServerRequestInterface $request, ResponseInterface $response): array
		{
			return [$request, $response];
		}

	};

	$eh = new EndpointHandler($controller::class, 'foobar');

	$endpoint = new Endpoint($eh);

	$container = Mockery::mock(Container::class);
	$container->shouldReceive('getByType')
		->once()
		->andReturn($controller);

	$sh = new ServiceHandler($container);

	$request = Psr7ServerRequestFactory::fromSuperGlobal()
		->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);
	$request = new ApiRequest($request);
	$response = new ApiResponse(Psr7ResponseFactory::fromGlobal());

	$res = $sh->handle($request, $response);

	Assert::same($request, $res[0]);
	Assert::same($response, $res[1]);
});
