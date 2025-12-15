<?php declare(strict_types = 1);

namespace Apitte\Core\LinkGenerator;

use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;

class LinkGenerator
{

	public function __construct(
		private Schema $schema,
	)
	{
	}

	/**
	 * Generate a link to an endpoint.
	 *
	 * @param string $destination Controller::method or endpoint ID
	 * @param array<string, mixed> $params Parameters to substitute in the path
	 * @return string The generated URL path
	 * @throws LinkGeneratorException
	 */
	public function link(string $destination, array $params = []): string
	{
		$endpoint = $this->findEndpoint($destination);

		if ($endpoint === null) {
			throw new LinkGeneratorException(sprintf('Endpoint "%s" not found', $destination));
		}

		return $this->buildUrl($endpoint, $params);
	}

	private function findEndpoint(string $destination): ?Endpoint
	{
		// Try to find by Controller::method format
		if (str_contains($destination, '::')) {
			return $this->findByControllerMethod($destination);
		}

		// Try to find by endpoint ID
		return $this->findById($destination);
	}

	private function findByControllerMethod(string $destination): ?Endpoint
	{
		[$class, $method] = explode('::', $destination, 2);

		foreach ($this->schema->getEndpoints() as $endpoint) {
			$handler = $endpoint->getHandler();

			if ($handler->getClass() === $class && $handler->getMethod() === $method) {
				return $endpoint;
			}
		}

		return null;
	}

	private function findById(string $id): ?Endpoint
	{
		foreach ($this->schema->getEndpoints() as $endpoint) {
			if ($endpoint->getTag(Endpoint::TAG_ID) === $id) {
				return $endpoint;
			}
		}

		return null;
	}

	/**
	 * @param array<string, mixed> $params
	 */
	private function buildUrl(Endpoint $endpoint, array $params): string
	{
		$mask = $endpoint->getMask();

		if ($mask === null) {
			throw new LinkGeneratorException('Endpoint has no mask defined');
		}

		// Get path parameters
		$pathParams = $endpoint->getParametersByIn(EndpointParameter::IN_PATH);

		// Substitute path parameters
		$url = $mask;
		foreach ($pathParams as $param) {
			$name = $param->getName();
			$placeholder = '{' . $name . '}';

			if (!array_key_exists($name, $params)) {
				if ($param->isRequired()) {
					throw new LinkGeneratorException(sprintf('Missing required parameter "%s"', $name));
				}

				continue;
			}

			/** @var scalar $value */
			$value = $params[$name];
			$url = str_replace($placeholder, (string) $value, $url);
			unset($params[$name]);
		}

		// Add remaining params as query string
		if ($params !== []) {
			$url .= '?' . http_build_query($params);
		}

		return $url;
	}

}
