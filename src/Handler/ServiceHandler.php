<?php declare(strict_types = 1);

namespace Apitte\Core\Handler;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Nette\DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceHandler implements IHandler
{

	/** @var Container */
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @return mixed
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response)
	{
		// Create and trigger callback
		$endpoint = $this->getEndpoint($request);
		$callback = $this->createCallback($endpoint);
		$response = $callback($request, $response);
		return $response;
	}

	protected function createCallback(Endpoint $endpoint): ServiceCallback
	{
		// Find handler in DI container by class
		$service = $this->getService($endpoint);
		$method = $endpoint->getHandler()->getMethod();

		// Create callback
		return new ServiceCallback($service, $method);
	}

	protected function getEndpoint(ServerRequestInterface $request): Endpoint
	{
		/** @var Endpoint|null $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		// Validate that we have an endpoint
		if (!$endpoint) {
			throw new InvalidStateException(sprintf('Attribute "%s" is required', RequestAttributes::ATTR_ENDPOINT));
		}

		return $endpoint;
	}

	/**
	 * @return object
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	protected function getService(Endpoint $endpoint)
	{
		return $this->container->getByType($endpoint->getHandler()->getClass());
	}

}
