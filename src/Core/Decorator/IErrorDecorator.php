<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface IErrorDecorator
{

	public function decorateError(ApiRequest $request, ApiResponse $response, ApiException $error): ApiResponse;

}
