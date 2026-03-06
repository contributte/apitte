<?php declare(strict_types = 1);

namespace Apitte\Core\Router;

use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\EndpointParameter;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Utils\NestedParameterResolver;
use Apitte\Core\Utils\Regex;

class SimpleRouter implements IRouter
{

	public function __construct(
		private readonly Schema $schema,
	)
	{
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
			if ($matched === null) {
				continue;
			}

			// If matched is not null, returns given ServerRequestInterface
			// with all parsed arguments and data,
			// also append given Endpoint
			return $matched
				->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint);
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
		/** @var array<string, string>|null $match */
		$match = Regex::match($url, $endpoint->getPattern());

		// Skip if there's no match
		if ($match === null) {
			return null;
		}

		$parameters = [];

		// Fill path parameters with matched variables
		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_PATH) as $param) {
			$name = $param->getName();
			$parameters[$name] = $match[$name] ?? null;
		}

		// Fill query parameters with query params
		// Supports JSON:API style nested parameters (e.g., page[number], filter[status], page:number)
		$queryParams = $request->getQueryParams();

		foreach ($endpoint->getParametersByIn(EndpointParameter::IN_QUERY) as $param) {
			$name = $param->getName();
			// Use NestedParameterResolver for bracket/colon notation support
			$parameters[$name] = NestedParameterResolver::getValue($queryParams, $name);
		}

		// Set attributes to request
		return $request
			->withAttribute(RequestAttributes::ATTR_ROUTER, $match)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, $parameters);
	}

}
