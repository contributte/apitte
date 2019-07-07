<?php declare(strict_types = 1);

namespace Apitte\Core\Dispatcher;

use Apitte\Core\Decorator\DecoratorManager;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Handler\IHandler;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Mapping\Response\IResponseEntity;
use Apitte\Core\Router\IRouter;
use Apitte\Negotiation\Http\AbstractEntity;
use Apitte\Negotiation\Http\ArrayEntity;
use Apitte\Negotiation\Http\MappingEntity;
use Apitte\Negotiation\Http\ScalarEntity;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class DecoratedDispatcher extends CoreDispatcher
{

	/** @var DecoratorManager */
	protected $decoratorManager;

	public function __construct(IRouter $router, IHandler $handler, DecoratorManager $decoratorManager)
	{
		parent::__construct($router, $handler);
		$this->decoratorManager = $decoratorManager;
	}

	public function dispatch(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			// Route and call handler
			$response = parent::dispatch($request, $response);
		} catch (Throwable $e) {

			// Get response from snapshot
			if ($e instanceof SnapshotException) {
				$request = $e->getRequest();
				$response = $e->getResponse();
				$e = $e->getPrevious();
			}

			// Pass only ApiException to error decorator
			if ($e instanceof ApiException) {
				$decoratedError = $e;
			} else {
				$decoratedError = ServerErrorException::create()
					->withPrevious($e);
			}

			// Trigger error decorator
			$response = $this->decoratorManager->decorateError($request, $response, $decoratedError);

			// Rethrow exception so error could be logged and transformed into response by error handler
			if ($response === null) {
				throw $e;
			}

			// Rethrow error with response from decorator so error could be logged and response returned
			throw new SnapshotException($e, $request, $response);
		}

		return $response;
	}

	protected function handle(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		// Pass endpoint to response
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT, null);
		if ($endpoint !== null) {
			$response = $response->withEndpoint($endpoint);
		}

		try {
			$request = $this->decoratorManager->decorateRequest($request, $response);
		} catch (EarlyReturnResponseException $exception) {
			return $exception->getResponse();
		}

		try {
			// If exception has been occurred during handling,
			// catch it and take a snapshot (SnapshotException)
			// of current request / response.
			// It's used for passing attributes to next layer (dispatch)
			// from decorators above (IDecorator::HANDLER_BEFORE).
			$result = $this->handler->handle($request, $response);
		} catch (Throwable $e) {
			throw new SnapshotException($e, $request, $response);
		}

		// If result is array convert it manually to ArrayEntity,
		// if result is scalar convert it manually to ScalarEntity,
		// if result is IResponseEntity convert it manually to MappingEntity,
		// otherwise use result as response
		if (is_array($result) || is_scalar($result) || $result instanceof IResponseEntity) {
			$response = $this->negotiate($result, $response);
		} else {
			// Validate if response is ResponseInterface
			if (!($result instanceof ResponseInterface)) {
				throw new InvalidStateException(sprintf('Endpoint returned response must implement "%s"', ResponseInterface::class));
			}

			if (!($result instanceof ApiResponse)) { //TODO - deprecation warning
				$result = new ApiResponse($result);
			}

			$response = $result;
		}

		try {
			$response = $this->decoratorManager->decorateResponse($request, $response);
		} catch (EarlyReturnResponseException $exception) {
			return $exception->getResponse();
		}

		return $response;
	}

	/**
	 * @param mixed $result
	 */
	protected function negotiate($result, ApiResponse $response): ApiResponse
	{
		if (!class_exists(AbstractEntity::class)) {
			throw new InvalidStateException(sprintf(
				'If you want return anything else than "%s" from your api endpoint then install "apitte/negotiation".',
				ApiResponse::class
			));
		}

		if (is_array($result)) {
			$response = $response->withEntity(ArrayEntity::from($result));
		} elseif (is_scalar($result)) {
			$response = $response->withEntity(ScalarEntity::from($result));
		} elseif ($result instanceof IResponseEntity) {
			$response = $response->withEntity(MappingEntity::from($result));
		}

		return $response;
	}

}
