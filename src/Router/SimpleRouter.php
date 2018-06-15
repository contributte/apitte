<?php declare(strict_types = 1);

namespace Apitte\Core\Router;

use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Utils\Regex;
use Psr\Http\Message\ServerRequestInterface;

class SimpleRouter implements IRouter
{

	/** @var Schema */
	private $schema;

	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	public function match(ServerRequestInterface $request): ?ServerRequestInterface
	{
		$endpoints = $this->schema->getEndpoints();

		// Iterate over all endpoints
		foreach ($endpoints as $endpoint) {
			$matched = $this->matchEndpoint($endpoint, $request);

			// Skip if endpoint is not matched
			if ($matched === null) continue;

			// If matched is not null, returns given ServerRequestInterface
			// with all parsed arguments and data,
			// also append given Endpoint
			$matched = $matched
				->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
				->withAttribute(RequestAttributes::ATTR_SCHEMA, $this->schema);

			return $matched;
		}

		return null;
	}

	protected function matchEndpoint(Endpoint $endpoint, ServerRequestInterface $request): ?ServerRequestInterface
	{
		// Skip unsupported HTTP method
		if (!$endpoint->hasMethod($request->getMethod())) {
			return null;
		}

		// Try match given URL (path) by build pattern
		$request = $this->compareUrl($endpoint, $request);

		return $request;
	}

	protected function compareUrl(Endpoint $endpoint, ServerRequestInterface $request): ?ServerRequestInterface
	{
		// Parse url from request
		$url = $request->getUri()->getPath();

		// Url has always slash at the beginning
		// and no trailing slash at the end
		$url = '/' . trim($url, '/');

		// Try to match against the pattern
		$match = Regex::match($url, $endpoint->getPattern());

		// Skip if there's no match
		if ($match === null) return null;

		$parameters = [];

		// Fill path parameters with matched variables
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_PATH) as $param) {
			$parameters[$param->getName()] = $match[$param->getName()];
		}

		// Fill query parameters with query params
		$queryParams = $request->getQueryParams();
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_QUERY) as $param) {
			$parameters[$param->getName()] = $queryParams[$param->getName()] ?? null;
		}

		// Set attributes to request
		$request = $request
			->withAttribute(RequestAttributes::ATTR_ROUTER, $match)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, $parameters);

		return $request;
	}

}
