<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Throwable;

class RethrowErrorDecorator implements IErrorDecorator
{

	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
	{
		throw $error;
	}

}
