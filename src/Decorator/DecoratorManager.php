<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class DecoratorManager
{

	/** @var IRequestDecorator[] */
	protected $requestDecorators = [];

	/** @var IResponseDecorator[] */
	protected $responseDecorators = [];

	/**
	 * @return static
	 */
	public function addRequestDecorator(IRequestDecorator $decorator): self
	{
		$this->requestDecorators[] = $decorator;
		return $this;
	}

	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		foreach ($this->requestDecorators as $decorator) {
			$request = $decorator->decorateRequest($request, $response);
		}

		return $request;
	}

	/**
	 * @return static
	 */
	public function addResponseDecorator(IResponseDecorator $decorator): self
	{
		$this->responseDecorators[] = $decorator;
		return $this;
	}

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		foreach ($this->responseDecorators as $decorator) {
			$response = $decorator->decorateResponse($request, $response);
		}

		return $response;
	}

}
