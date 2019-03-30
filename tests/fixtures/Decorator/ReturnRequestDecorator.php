<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ReturnRequestDecorator implements IRequestDecorator
{

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		return $request;
	}

}
