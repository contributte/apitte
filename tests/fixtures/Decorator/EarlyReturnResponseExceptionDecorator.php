<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class EarlyReturnResponseExceptionDecorator implements IRequestDecorator, IResponseDecorator
{

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		throw new EarlyReturnResponseException($response);
	}

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		throw new EarlyReturnResponseException($response);
	}

}
