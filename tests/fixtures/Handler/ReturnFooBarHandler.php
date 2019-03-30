<?php declare(strict_types = 1);

namespace Tests\Fixtures\Handler;

use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class ReturnFooBarHandler implements IHandler
{

	/**
	 * @return mixed[]
	 */
	public function handle(ApiRequest $request, ApiResponse $response): array
	{
		return ['foo', 'bar'];
	}

}
