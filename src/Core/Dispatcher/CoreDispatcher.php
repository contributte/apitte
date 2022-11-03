<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Router\IRouter;
use Psr\Http\Message\ResponseInterface;

class CoreDispatcher implements IDispatcher
{

	protected IRouter $router;

	protected IHandler $handler;

	public function __construct(IRouter $router, IHandler $handler)
	{
		$this->router = $router;
		$this->handler = $handler;
	}

	public function dispatch(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		// Try match request to our routes
		$matchedRequest = $this->match($request, $response);

		// If there is no match route <=> endpoint,
		if ($matchedRequest === null) {
			return $this->fallback($request, $response);
		}

		// According to matched endpoint, forward to handler
		return $this->handle($matchedRequest, $response);
	}

	protected function match(ApiRequest $request, ApiResponse $response): ?ApiRequest
	{
		return $this->router->match($request);
	}

	protected function handle(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $this->handler->handle($request, $response);

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
		}

		if (!($response instanceof ApiResponse)) { //TODO - deprecation warning
			$response = new ApiResponse($response);
		}

		return $response;
	}

	protected function fallback(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		throw new ClientErrorException('No matched route by given URL', 404);
	}

}
