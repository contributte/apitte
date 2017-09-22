<?php

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Handler\Decorator\IDecorator;
use Apitte\Core\Handler\Decorator\IRequestDecorator;
use Apitte\Core\Handler\Decorator\IResponseDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DecoratedServiceHandler extends ServiceHandler
{

	/** @var IRequestDecorator[] */
	protected $requestDecorators = [];

	/** @var IResponseDecorator[] */
	protected $responseDecorators = [];

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * @param IRequestDecorator $decorator
	 * @return void
	 */
	public function addRequestDecorator(IRequestDecorator $decorator)
	{
		$this->requestDecorators[] = $decorator;
	}

	/**
	 * @param IResponseDecorator $decorator
	 * @return void
	 */
	public function addResponseDecorator(IResponseDecorator $decorator)
	{
		$this->responseDecorators[] = $decorator;
	}

	/**
	 * @param IDecorator[] $decorators
	 * @return void
	 */
	public function addDecorators(array $decorators)
	{
		foreach ($decorators as $decorator) {
			if ($decorator instanceof IRequestDecorator) {
				$this->addRequestDecorator($decorator);
			}
			if ($decorator instanceof IResponseDecorator) {
				$this->addResponseDecorator($decorator);
			}
		}
	}

	/**
	 * API *********************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		// Trigger request decorator
		$request = $this->beforeHandle($request, $response);

		// Handle request
		$response = parent::handle($request, $response);

		// Trigger response decorator
		$response = $this->afterHandle($request, $response);

		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ServerRequestInterface
	 */
	protected function beforeHandle(ServerRequestInterface $request, ResponseInterface $response)
	{
		foreach ($this->requestDecorators as $decorator) {
			$request = $decorator->decorateRequest($request);

			// Validate if response is returned
			if (!$request) {
				throw new InvalidStateException(sprintf('RequestDecorator "%s" must return request', get_class($decorator)));
			}

			// Validate if response is ApiResponse
			if (!($request instanceof ServerRequestInterface)) {
				throw new InvalidStateException(sprintf('RequestDecorator returned request must be subtype of %s', ServerRequestInterface::class));
			}
		}

		return $request;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function afterHandle(ServerRequestInterface $request, ResponseInterface $response)
	{
		foreach ($this->responseDecorators as $decorator) {
			$response = $decorator->decorateResponse($response);

			// Validate if response is returned
			if (!$response) {
				throw new InvalidStateException(sprintf('ResponseDecorator "%s" must return response', get_class($decorator)));
			}

			// Validate if response is ApiResponse
			if (!($response instanceof ResponseInterface)) {
				throw new InvalidStateException(sprintf('ResponseDecorator returned response must be subtype of %s', ResponseInterface::class));
			}
		}

		return $response;
	}

}
