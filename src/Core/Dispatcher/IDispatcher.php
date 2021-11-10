<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IDispatcher
{

	public function dispatch(ApiRequest $request, ApiResponse $response): ApiResponse;

}
