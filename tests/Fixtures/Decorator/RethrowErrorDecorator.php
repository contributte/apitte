<?php declare(strict_types = 1);

namespace Tests\Fixtures\Decorator;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class RethrowErrorDecorator implements IErrorDecorator
{

	public function decorateError(ApiRequest $request, ApiResponse $response, ApiException $error): ApiResponse
	{
		throw $error;
	}

}
