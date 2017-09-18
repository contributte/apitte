<?php

namespace Apitte\Core\UI;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IHandler
{

	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function handle(ApiRequest $request, ApiResponse $response);

}
