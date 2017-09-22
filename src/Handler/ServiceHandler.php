<?php

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Nette\DI\Container;
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
		/** @var Endpoint $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		// Find handler in DI container by class
		$service = $this->container->getByType($endpoint->getHandler()->getClass());
		$method = $endpoint->getHandler()->getMethod();

		// Call service::method with ($request, $response) as arguments
		$response = call_user_func_array(
			[$service, $method],
			[$this->createApiRequest($request), $this->createApiResponse($response)]
		);

		// Validate if response is returned
		if (!$response) {
			throw new InvalidStateException(sprintf('Handler "%s::%s()" must return response', get_class($service), $method));
		}

		// Convert ApiResponse to ResponseInterface
		if ($response instanceof ApiResponse) {
			// Get original response
			$response = $response->getOriginalResponse();
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
	 * @return ApiRequest
	 */
	protected function createApiRequest(ServerRequestInterface $request)
	{
		return new ApiRequest($request);
	}

	/**
	 * @param ResponseInterface $response
	 * @return ApiResponse
	 */
	protected function createApiResponse(ResponseInterface $response)
	{
		return new ApiResponse($response);
	}

}
