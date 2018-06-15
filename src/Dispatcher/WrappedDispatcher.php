<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WrappedDispatcher implements IDispatcher
{

	/** @var IDispatcher */
	protected $inner;

	public function __construct(IDispatcher $inner)
	{
		$this->inner = $inner;
	}

	public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		// Create API/HTTP objects
		$request = $this->createApiRequest($request);
		$response = $this->createApiResponse($response);

		// Dispatch our classes
		$response = $this->inner->dispatch($request, $response);

		// Unwrap response
		$response = $this->unwrap($response);

		return $response;
	}

	protected function createApiRequest(ServerRequestInterface $request): ApiRequest
	{
		return new ApiRequest($request);
	}

	protected function createApiResponse(ResponseInterface $response): ApiResponse
	{
		return new ApiResponse($response);
	}

	protected function unwrap(ResponseInterface $response): ResponseInterface
	{
		if ($response instanceof ApiResponse) {
			// Get original response
			return $response->getOriginalResponse();
		}

		return $response;
	}

}
