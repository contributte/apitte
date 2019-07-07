<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ReturnResponseDecorator implements IResponseDecorator
{

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

}
