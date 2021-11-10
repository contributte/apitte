<?php declare(strict_types = 1);

namespace Apitte\Middlewares;

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ApiMiddleware implements IMiddleware
{

	/** @var IDispatcher */
	protected $dispatcher;

	/** @var IErrorHandler */
	protected $errorHandler;

	public function __construct(IDispatcher $dispatcher, IErrorHandler $errorHandler)
	{
		$this->dispatcher = $dispatcher;
		$this->errorHandler = $errorHandler;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		if (!$request instanceof ApiRequest) {
			$request = new ApiRequest($request);
		}

		if (!$response instanceof ApiResponse) {
			$response = new ApiResponse($response);
		}

		// Pass this API request/response objects to API dispatcher
		try {
			$response = $this->dispatcher->dispatch($request, $response);
		} catch (Throwable $exception) {
			$response = $this->errorHandler->handle(new DispatchError($exception, $request));
		}

		// Pass response to next middleware
		$response = $next($request, $response);

		return $response;
	}

}
