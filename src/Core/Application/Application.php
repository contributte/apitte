<?php declare(strict_types = 1);

namespace Apitte\Core\Application;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Contributte\Psr7\Psr7Response;

class Application extends BaseApplication
{

	private IDispatcher $dispatcher;

	public function __construct(IErrorHandler $errorHandler, IDispatcher $dispatcher)
	{
		parent::__construct($errorHandler);
		$this->dispatcher = $dispatcher;
	}

	protected function dispatch(ApiRequest $request): ApiResponse
	{
		return $this->dispatcher->dispatch($request, new ApiResponse(new Psr7Response()));
	}

}
