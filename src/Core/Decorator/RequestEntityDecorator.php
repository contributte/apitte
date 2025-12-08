<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Mapping\RequestEntityMapping;

class RequestEntityDecorator implements IRequestDecorator
{

	public function __construct(
		protected RequestEntityMapping $mapping,
	)
	{
	}

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		return $this->mapping->map($request, $response);
	}

}
