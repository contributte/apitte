<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class WrappedDispatcher implements IDispatcher
{

	/** @var IDispatcher */
	protected $inner;

	/** @var IErrorHandler */
	private $errorHandler;

	public function __construct(IDispatcher $inner, IErrorHandler $errorHandler)
	{
		$this->inner = $inner;
		$this->errorHandler = $errorHandler;
	}

	public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		// Create API/HTTP objects
		$request = $this->createApiRequest($request);
		$response = $this->createApiResponse($response);

		try {
			// Dispatch our classes
			$response = $this->inner->dispatch($request, $response);
		} catch (Throwable $exception) {
			// Log exception
			$this->errorHandler->handle($exception);

			// Return response from exception if possible (returned by DecoratedDispatcher)
			if ($exception instanceof SnapshotException) {
				return $exception->getResponse();
			}
		}

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
