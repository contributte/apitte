<?php declare(strict_types = 1);

namespace Tests\Fixtures\Dispatcher;

use Apitte\Core\Dispatcher\IDispatcher;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class FakeDispatcher implements IDispatcher
{

	public function dispatch(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

}
