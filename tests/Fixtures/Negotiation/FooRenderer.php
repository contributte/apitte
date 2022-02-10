<?php declare(strict_types = 1);

namespace Tests\Fixtures\Negotiation;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class FooRenderer
{

	/**
	 * @param mixed[] $context
	 */
	public function __invoke(ApiRequest $request, ApiResponse $response, array $context): ApiResponse
	{
	}

}
