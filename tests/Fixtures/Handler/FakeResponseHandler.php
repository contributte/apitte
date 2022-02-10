<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class FakeResponseHandler implements IHandler
{

	public function handle(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}

}
