<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Utils\Helpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class WrappedDispatcher implements IDispatcher
{

	/** @var IDispatcher */
	protected $inner;

	/** @var LoggerInterface */
	private $logger;

	/** @var bool */
	private $catchExceptions;

	public function __construct(IDispatcher $inner, LoggerInterface $logger, bool $catchExceptions = false)
	{
		$this->inner = $inner;
		$this->logger = $logger;
		$this->catchExceptions = $catchExceptions;
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
			$this->logger->error($exception->getMessage(), ['exception' => Helpers::throwableToArray($exception)]);

			// Rethrow exception if it should not be catch (debug only)
			if (!$this->catchExceptions) {
				if ($exception instanceof SnapshotException) {
					throw $exception->getPrevious();
				}
				throw $exception;
			}

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
