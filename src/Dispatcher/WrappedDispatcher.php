<?php

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WrappedDispatcher implements IDispatcher
{

	/** @var IDispatcher */
	protected $inner;

	/**
	 * @param IDispatcher $inner
	 */
	public function __construct(IDispatcher $inner)
	{
		$this->inner = $inner;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
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

	/**
	 * @param ServerRequestInterface $request
	 * @return ApiRequest
	 */
	protected function createApiRequest(ServerRequestInterface $request)
	{
		return new ApiRequest($request);
	}

	/**
	 * @param ResponseInterface $response
	 * @return ApiResponse
	 */
	protected function createApiResponse(ResponseInterface $response)
	{
		return new ApiResponse($response);
	}

	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function unwrap(ResponseInterface $response)
	{
		if ($response instanceof ApiResponse) {
			// Get original response
			return $response->getOriginalResponse();
		}

		return $response;
	}

}
