<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Mapping\RequestParameterMapping;

class RequestParametersDecorator implements IRequestDecorator
{

	/** @var RequestParameterMapping */
	protected $mapping;

	public function __construct(RequestParameterMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		return $this->mapping->map($request, $response);
	}

}
