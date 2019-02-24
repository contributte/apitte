<?php declare(strict_types = 1);

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class DecoratorManager
{

	/** @var IRequestDecorator[] */
	protected $requestDecorators = [];

	/** @var IResponseDecorator[] */
	protected $responseDecorators = [];

	/** @var IErrorDecorator[] */
	protected $errorDecorators = [];

	/**
	 * @return static
	 */
	public function addRequestDecorator(IRequestDecorator $decorator): self
	{
		$this->requestDecorators[] = $decorator;
		return $this;
	}

	public function decorateRequest(ServerRequestInterface $request, ResponseInterface $response): ServerRequestInterface
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

	public function decorateResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

	public function decorateError(ServerRequestInterface $request, ResponseInterface $response, Throwable $error): ?ResponseInterface
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
