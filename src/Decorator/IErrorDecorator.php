<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Throwable;

interface IErrorDecorator
{

	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse;

}
