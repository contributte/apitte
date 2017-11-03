<?php

namespace Apitte\Core\Decorator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DecoratorManager
{

	/** @var [IDecorator[]] */
	protected $decorators = [];

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * @param string $type
	 * @param IDecorator $decorator
	 * @return void
	 */
	public function addDecorator($type, IDecorator $decorator)
	{
		if (!isset($this->decorators[$type])) {
			$this->decorators[$type] = [];
		}

		$this->decorators[$type][] = $decorator;
	}

	/**
	 * EMITTING ****************************************************************
	 */

	/**
	 * @param string $type
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $context
	 * @return ServerRequestInterface
	 */
	public function decorateRequest($type, ServerRequestInterface $request, ResponseInterface $response, array $context = [])
	{
		$decorators = isset($this->decorators[$type]) ? $this->decorators[$type] : [];

		/** @var IDecorator $decorator */
		foreach ($decorators as $decorator) {
			$request = $decorator->decorate($request, $response, $context);
		}

		return $request;
	}

	/**
	 * @param string $type
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param array $context
	 * @return ResponseInterface
	 */
	public function decorateResponse($type, ServerRequestInterface $request, ResponseInterface $response, array $context = [])
	{
		$decorators = isset($this->decorators[$type]) ? $this->decorators[$type] : [];

		/** @var IDecorator $decorator */
		foreach ($decorators as $decorator) {
			$response = $decorator->decorate($request, $response, $context);
		}

		return $response;
	}

}
