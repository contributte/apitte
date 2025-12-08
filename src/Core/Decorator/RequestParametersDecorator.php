<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Mapping\RequestParameterMapping;

class RequestParametersDecorator implements IRequestDecorator
{

	public function __construct(
		protected RequestParameterMapping $mapping,
	)
	{
	}

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		return $this->mapping->map($request, $response);
	}

}
