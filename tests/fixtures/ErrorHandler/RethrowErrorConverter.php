<?php declare(strict_types = 1);

namespace Tests\Fixtures\ErrorHandler;

use Apitte\Core\ErrorHandler\ErrorConverter;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class RethrowErrorConverter implements ErrorConverter
{

	public function createResponseFromError(ApiException $error, ?ApiRequest $request = null, ?ApiResponse $response = null): ApiResponse
	{
		throw $error;
	}

}
