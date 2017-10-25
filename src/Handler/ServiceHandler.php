<?php

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Nette\DI\Container;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceHandler implements IHandler
{

	/** @var Container */
	protected $container;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		// Create and trigger callback
		$callback = $this->createCallback($request, $response);
		$response = $callback($request, $response);

		// Validate if response is returned
		if ($response === NULL) {
			throw new InvalidStateException('Handler returned response cannot be NULL');
		}

		// Convert array to JSON
		if (is_array($response)) {
			$response->getBody()->write(Json::encode($response));
		}

		// Validate if response is ResponseInterface
		if (!($response instanceof ResponseInterface)) {
			throw new InvalidStateException(sprintf('Handler returned response must be subtype of %s', ResponseInterface::class));
		}

		return $response;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ServiceCallback
	 */
	protected function createCallback(ServerRequestInterface $request, ResponseInterface $response)
	{
		$endpoint = $this->getEndpoint($request);

		// Find handler in DI container by class
		$service = $this->getService($endpoint);
		$method = $endpoint->getHandler()->getMethod();

		// Create callback
		$callback = new ServiceCallback($service, $method);
		$callback->setArguments([$request, $response]);

		return $callback;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return Endpoint
	 */
	protected function getEndpoint(ServerRequestInterface $request)
	{
		/** @var Endpoint $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		return $endpoint;
	}

	/**
	 * @param Endpoint $endpoint
	 * @return object
	 */
	protected function getService(Endpoint $endpoint)
	{
		return $this->container->getByType($endpoint->getHandler()->getClass());
	}

}
