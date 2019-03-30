<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IResponseDecorator
{

	/**
	 * @throws EarlyReturnResponseException If other response decorators should be skipped
	 */
	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse;

}
