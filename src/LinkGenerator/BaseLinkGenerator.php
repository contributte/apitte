<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\Exception\Logical\InvalidLinkException;
use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\RequestScopeStorage;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Schema;
use Psr\Http\Message\UriInterface;

abstract class BaseLinkGenerator implements LinkGenerator
{

	/** @var Schema */
	protected $schema;

	/** @var RequestScopeStorage */
	protected $requestScopeStorage;

	/** @var Endpoint[][] */
	private $endpointCache = [];

	/** @var ControllerMapper */
	private $controllerMapper;

	public function __construct(Schema $schema, RequestScopeStorage $requestScopeStorage, ControllerMapper $mapper)
	{
		$this->schema = $schema;
		$this->requestScopeStorage = $requestScopeStorage;
		$this->controllerMapper = $mapper;
	}

	/**
	 * @param string  $destination "[[module:]controller:action]"
	 * @param mixed[] $parameters
	 */
	public function link(string $destination, array $parameters = []): string
	{
		if (!preg_match('~^([\w:]+):(\w*+)(#.*)?()\z~', $destination, $m)) {
			throw new InvalidLinkException(sprintf('Invalid link destination "%s".', $destination));
		}

		[, $controller, $method, $fragment] = $m;

		$class = $this->controllerMapper->getControllerClass($controller);
		$endpoint = $this->findEndpointByClassAndMethod($class, $method);

		return $this->buildUrl($endpoint, $parameters, $fragment);
	}

	/**
	 * @param mixed[] $parameters
	 */
	abstract protected function buildUrl(Endpoint $endpoint, array $parameters, string $fragment): string;

	protected function getBaseUri(): string
	{
		/** @var UriInterface|null $uri */
		$uri = $this->requestScopeStorage->load('uri');

		if ($uri === null) {
			return '';
		}

		return $uri->getHost();
	}

	private function findEndpointByClassAndMethod(string $class, string $method): Endpoint
	{
		if (isset($this->endpointCache[$class][$method])) {
			return $this->endpointCache[$class][$method];
		}

		$classFound = false;

		foreach ($this->schema->getEndpoints() as $endpoint) {
			$handler = $endpoint->getHandler();
			if ($handler->getClass() === $class && $handler->getMethod() === $method) {
				$this->endpointCache[$class][$method] = $endpoint;
				return $endpoint;
			}
			if ($handler->getClass() === $class) {
				$classFound = true;
			}
		}

		if ($classFound) {
			throw new InvalidStateException(sprintf('Controllers "%s" method "%s" is missing in schema.', $class, $method));
		}

		throw new InvalidStateException(sprintf('Controller "%s" is missing in schema.', $class));
	}

}
