<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
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

	public function dispatch(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			// Dispatch our classes
			$response = $this->inner->dispatch($request, $response);
		} catch (Throwable $exception) {
			// Process exception so it could be logged and transformed into response
			$response = $this->errorHandler->handle($exception);
		}

		return $response;
	}

}
