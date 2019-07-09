<?php declare(strict_types = 1);

namespace Apitte\Core\ErrorHandling;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

interface ErrorConverter
{

	public function createResponseFromError(ApiException $error, ?ApiRequest $request = null, ?ApiResponse $response = null): ApiResponse;

}
