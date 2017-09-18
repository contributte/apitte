<?php

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IDispatcher
{

	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function dispatch(ApiRequest $request, ApiResponse $response);

}
