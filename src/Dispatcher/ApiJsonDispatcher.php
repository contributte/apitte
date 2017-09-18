<?php

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ApiJsonDispatcher extends ApiDispatcher
{

	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	protected function fallback(ApiRequest $request, ApiResponse $response)
	{
		return $response
			->withStatus(404)
			->writeJsonBody(['error' => 'No matched route by given URL']);
	}

}
