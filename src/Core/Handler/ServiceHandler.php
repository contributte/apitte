<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidArgumentException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\UI\Controller\IController;
use Nette\DI\Container;

class ServiceHandler implements IHandler
{

	protected Container $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return mixed
	 */
	public function handle(ApiRequest $request, ApiResponse $response)
	{
		// Create and trigger callback
		$endpoint = $this->getEndpoint($request);
		$callback = $this->createCallback($endpoint);
		return $callback($request, $response);
	}

	protected function createCallback(Endpoint $endpoint): ServiceCallback
	{
		// Find handler in DI container by class
		$service = $this->getService($endpoint);
		$method = $endpoint->getHandler()->getMethod();

		// Create callback
		return new ServiceCallback($service, $method);
	}

	protected function getEndpoint(ApiRequest $request): Endpoint
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if ($endpoint === null) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		return $endpoint;
	}

	protected function getService(Endpoint $endpoint): IController
	{
		$class = $endpoint->getHandler()->getClass();
		$service = $this->container->getByType($class);

		if (!($service instanceof IController)) {
			throw new InvalidArgumentException(sprintf('Controller "%s" must implement "%s"', $class, IController::class));
		}

		return $service;
	}

}
