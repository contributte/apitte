<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Apitte\Core\Exception\ApiException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class DecoratorManager
{

	/** @var IRequestDecorator[] */
	protected array $requestDecorators = [];

	/** @var IResponseDecorator[] */
	protected array $responseDecorators = [];

	/** @var IErrorDecorator[] */
	protected array $errorDecorators = [];

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

	/**
	 * @return static
	 */
	public function addErrorDecorator(IErrorDecorator $decorator): self
	{
		$this->errorDecorators[] = $decorator;
		return $this;
	}

	public function decorateError(ApiRequest $request, ApiResponse $response, ApiException $error): ?ApiResponse
	{
		// If there is no exception handler defined so return null (and exception will be thrown in DecoratedDispatcher)
		if ($this->errorDecorators === []) {
			return null;
		}

		foreach ($this->errorDecorators as $decorator) {
			$response = $decorator->decorateError($request, $response, $error);
		}

		return $response;
	}

}
