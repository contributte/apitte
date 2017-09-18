<?php

namespace Apitte\Core\Router;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Schema\ApiSchema;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Utils\Regex;

class ApiRouter implements IRouter
{

	/** @var ApiSchema */
	private $schema;

	/**
	 * @param ApiSchema $schema
	 */
	public function __construct(ApiSchema $schema)
	{
		$this->schema = $schema;
	}

	/**
	 * @param ApiRequest $request
	 * @return ApiRequest|NULL
	 */
	public function match(ApiRequest $request)
	{
		$endpoints = $this->schema->getEndpoints();

		// Iterate over all endpoints
		foreach ($endpoints as $endpoint) {
			$matched = $this->matchEndpoint($endpoint, $request);

			// Skip if endpoint is not matched
			if ($matched === NULL) continue;

			// If matched is not NULL, returns given ApiRequest
			// with all parsed arguments and data,
			// also append given Endpoint
			$matched = $matched->withEndpoint($endpoint);

			return $matched;
		}

		return NULL;
	}

	/**
	 * @param Endpoint $endpoint
	 * @param ApiRequest $request
	 * @return ApiRequest|NULL
	 */
	protected function matchEndpoint(Endpoint $endpoint, ApiRequest $request)
	{
		// Skip unsupported HTTP method
		if (!$endpoint->hasMethod($request->getMethod())) {
			return NULL;
		}

		// Try match given URL (path) by build pattern
		$request = $this->compareUrl($endpoint, $request);

		return $request;
	}

	/**
	 * @param Endpoint $endpoint
	 * @param ApiRequest $request
	 * @return ApiRequest|NULL
	 */
	protected function compareUrl(Endpoint $endpoint, ApiRequest $request)
	{
		// Parse url from ApiRequest
		$url = $request->getUri()->getPath();

		// Url has always slash at the beginning
		// and no trailing slash at the end
		$url = '/' . trim($url, '/');

		// Try to match against the pattern
		$match = Regex::match($url, $endpoint->getPattern());

		// Skip if there's no match
		if ($match === NULL) return NULL;

		// Fill ApiRequest attributes with matched variables
		$request = $request->withAttribute(ApiRequest::ATTR_ROUTER, $match);

		// Fill ApiRequest parameters with matched variables
		foreach ($endpoint->getParameters() as $param) {
			$request = $request->withParameter($param->getName(), $match[$param->getName()]);
		}

		return $request;
	}

}
