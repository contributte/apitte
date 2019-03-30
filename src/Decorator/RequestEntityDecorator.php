<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Mapping\RequestEntityMapping;

class RequestEntityDecorator implements IRequestDecorator
{

	/** @var RequestEntityMapping */
	protected $mapping;

	public function __construct(RequestEntityMapping $mapping)
	{
		$this->mapping = $mapping;
	}

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		return $this->mapping->map($request, $response);
	}

}
