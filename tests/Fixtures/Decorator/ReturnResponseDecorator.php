<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ReturnResponseDecorator implements IResponseDecorator, IErrorDecorator
{

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

	public function decorateError(ApiRequest $request, ApiResponse $response, ApiException $error): ApiResponse
	{
		return $response;
	}

}
