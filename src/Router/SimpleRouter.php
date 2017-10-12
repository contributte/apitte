<?php

namespace Apitte\Core\Router;

use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use Apitte\Core\Schema\Schema;
use Apitte\Core\Utils\Regex;
use Psr\Http\Message\ServerRequestInterface;

class SimpleRouter implements IRouter
{

	/** @var Schema */
	private $schema;

	/**
	 * @param Schema $schema
	 */
	public function __construct(Schema $schema)
	{
		$this->schema = $schema;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface|NULL
	 */
	public function match(ServerRequestInterface $request)
	{
		$endpoints = $this->schema->getEndpoints();

		// Iterate over all endpoints
		foreach ($endpoints as $endpoint) {
			$matched = $this->matchEndpoint($endpoint, $request);

			// Skip if endpoint is not matched
			if ($matched === NULL) continue;

			// If matched is not NULL, returns given ServerRequestInterface
			// with all parsed arguments and data,
			// also append given Endpoint
			$matched = $matched
				->withAttribute(RequestAttributes::ATTR_ENDPOINT, $endpoint)
				->withAttribute(RequestAttributes::ATTR_SCHEMA, $this->schema);

			return $matched;
		}

		return NULL;
	}

	/**
	 * @param Endpoint $endpoint
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface|NULL
	 */
	protected function matchEndpoint(Endpoint $endpoint, ServerRequestInterface $request)
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
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface|NULL
	 */
	protected function compareUrl(Endpoint $endpoint, ServerRequestInterface $request)
	{
		// Parse url from request
		$url = $request->getUri()->getPath();

		// Url has always slash at the beginning
		// and no trailing slash at the end
		$url = '/' . trim($url, '/');

		// Try to match against the pattern
		$match = Regex::match($url, $endpoint->getPattern());

		// Skip if there's no match
		if ($match === NULL) return NULL;

		// Fill parameters with matched variables
		$parameters = [];
		foreach ($endpoint->getParameters() as $param) {
			$parameters[$param->getName()] = $match[$param->getName()];
		}

		// Set attributes to request
		$request = $request
			->withAttribute(RequestAttributes::ATTR_ROUTER, $match)
			->withAttribute(RequestAttributes::ATTR_PARAMETERS, $parameters);

		return $request;
	}

}
