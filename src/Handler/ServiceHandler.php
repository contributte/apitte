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
	private $container;

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

		if (!$endpoint) {
			throw new InvalidStateException('Endpoint attribute is required');
		}

		$handler = $endpoint->getHandler();

		// Find handler in DI container by class
		$service = $this->container->getByType($handler->getClass());
		$method = $handler->getMethod();

		$apiRequest = new ApiRequest($request);
		$apiResponse = new ApiResponse($response);

		// Call service::method with ($request, $response) as arguments
		$response = call_user_func_array(
			[$service, $method],
			[$apiRequest, $apiResponse]
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

}
