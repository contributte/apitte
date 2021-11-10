<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IRequestDecorator
{

	/**
	 * @throws EarlyReturnResponseException If other request decorators and also deeper layers (endpoint) should be skipped
	 */
	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest;

}
