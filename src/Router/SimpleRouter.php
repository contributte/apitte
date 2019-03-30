<?php declare(strict_types = 1);

namespace Apitte\Core\Router;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Utils\Regex;

class SimpleRouter implements IRouter
{

	/** @var Schema */
	private $schema;

	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	public function match(ApiRequest $request): ?ApiRequest
	{
		$endpoints = $this->schema->getEndpoints();

		$exception = null;
		$matched = null;
		// Iterate over all endpoints
		foreach ($endpoints as $endpoint) {
			try {
				$matched = $this->matchEndpoint($endpoint, $request);
			} catch (ClientErrorException $exception) {
				// Don't throw exception unless we know there is no endpoint with same mask which support requested http method
			}

			// Skip if endpoint is not matched
			if ($matched === null) continue;

			// If matched is not null, returns given ServerRequestInterface
			// with all parsed arguments and data,
			// also append given Endpoint
			$matched = $matched
				->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);

			return $matched;
		}

		if ($exception !== null) {
			throw $exception;
		}

		return null;
	}

	protected function matchEndpoint(Endpoint $endpoint, ApiRequest $request): ?ApiRequest
	{
		// Try match given URL (path) by build pattern
		$request = $this->compareUrl($endpoint, $request);

		// Skip unsupported HTTP method
		if ($request !== null && !$endpoint->hasMethod($request->getMethod())) {
			throw new ClientErrorException(sprintf('Method "%s" is not allowed for endpoint "%s".', $request->getMethod(), $endpoint->getMask()), 405);
		}

		return $request;
	}

	protected function compareUrl(Endpoint $endpoint, ApiRequest $request): ?ApiRequest
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
			$name = $param->getName();
			$parameters[$name] = $match[$name];
		}

		// Fill query parameters with query params
		$queryParams = $request->getQueryParams();
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_QUERY) as $param) {
			$name = $param->getName();
			$parameters[$name] = $queryParams[$name] ?? null;
		}

		// Set attributes to request
		$request = $request
			->withAttribute(RequestAttributes::ATTR_ROUTER, $match)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, $parameters);

		return $request;
	}

}
