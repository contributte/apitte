<?php

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Decorator\IDecorator;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Router\IRouter;
use Apitte\Negotiation\Http\ArrayEntity;
use Apitte\Negotiation\Http\ScalarEntity;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DecoratedDispatcher extends CoreDispatcher
{

	/** @var DecoratorManager */
	protected $decoratorManager;

	/**
	 * @param IRouter $router
	 * @param IHandler $handler
	 * @param DecoratorManager $decoratorManager
	 */
	public function __construct(IRouter $router, IHandler $handler, DecoratorManager $decoratorManager)
	{
		parent::__construct($router, $handler);
		$this->decoratorManager = $decoratorManager;
	}

	/**
	 * @param ApiRequest|ServerRequestInterface $request
	 * @param ApiResponse|ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
	{
		try {
			// Trigger request decorator
			$request = $this->decoratorManager->decorateRequest(IDecorator::DISPATCHER_BEFORE, $request, $response);
		} catch (EarlyReturnResponseException $exception) {
			return $exception->getResponse();
		}

		try {
			// Route and call handler
			$response = parent::dispatch($request, $response);

			// Trigger response decorator
			$response = $this->decoratorManager->decorateResponse(IDecorator::DISPATCHER_AFTER, $request, $response);
		} catch (SnapshotException $e) {
			// Mine data from snapshot
			$request = $e->getRequest();
			$response = $e->getResponse();
			$e = $e->getPrevious();

			// Trigger exception decorator
			$response = $this->decoratorManager->decorateResponse(IDecorator::DISPATCHER_EXCEPTION, $request, $response, ['exception' => $e]);

			// If there's no decorator to handle this exception, throw again
			if ($response === NULL) throw $e;
		} catch (Exception $e) {
			// Trigger exception decorator
			$response = $this->decoratorManager->decorateResponse(IDecorator::DISPATCHER_EXCEPTION, $request, $response, ['exception' => $e]);

			// If there's no decorator to handle this exception, throw again
			if ($response === NULL) throw $e;
		}

		return $response;
	}

	/**
	 * @param ApiRequest|ServerRequestInterface $request
	 * @param ApiResponse|ResponseInterface $response
	 * @return ResponseInterface
	 */
	protected function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		try {
			// Trigger request decorator
			$request = $this->decoratorManager->decorateRequest(IDecorator::HANDLER_BEFORE, $request, $response);
		} catch (EarlyReturnResponseException $exception) {
			return $exception->getResponse();
		}

		// Pass endpoint
		if (($endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT, NULL))) {
			$response = $response->withEndpoint($endpoint);
		}

		try {
			// If exception has been occurred during handling,
			// catch it and take a snapshot (SnapshotException)
			// of current request / response.
			// It's used for passing attributes to next layer (dispatch)
			// from decorators above (IDecorator::HANDLER_BEFORE).
			$result = $this->handler->handle($request, $response);
		} catch (Exception $e) {
			throw new SnapshotException($e, $request, $response);
		}

		// If result is array convert it manually to ArrayEntity,
		// if result is scalar convert it manually to ScalarEntity,
		// otherwise use result as response
		if (is_array($result)) {
			$response = $response->withEntity(ArrayEntity::from($result));
		} else if (is_scalar($result)) {
			$response = $response->withEntity(ScalarEntity::from($result));
		} else {
			$response = $result;
		}

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Handler returned response must implement "%s"', ResponseInterface::class));
		}

		return $response;
	}

}
