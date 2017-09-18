<?php

namespace Apitte\Core\Http;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Contributte\Psr7\Psr7ServerRequest;

class ApiRequest extends Psr7ServerRequest
{

	// Attributes
	const ATTR_ROUTER = 'C-Router';
	const ATTR_PARAMETERS = 'C-Parameters';

	/** @var Endpoint */
	protected $endpoint;

	/**
	 * ENDPOINT ****************************************************************
	 */

	/**
	 * @param Endpoint $endpoint
	 * @return static
	 */
	public function withEndpoint(Endpoint $endpoint)
	{
		$new = clone $this;
		$new->endpoint = $endpoint;

		return $new;
	}

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	/**
	 * PARAMETERS **************************************************************
	 */

	/**
	 * @param array $parameters
	 * @return static
	 */
	public function withParameters(array $parameters)
	{
		return $this->withAttribute(self::ATTR_PARAMETERS, $parameters);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return static
	 */
	public function withParameter($name, $value)
	{
		$parameters = $this->getAttribute(self::ATTR_PARAMETERS, []);
		$parameters[$name] = $value;

		return $this->withParameters($parameters);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name)
	{
		return isset($this->getAttribute(self::ATTR_PARAMETERS, [])[$name]);
	}

	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getParameter($name, $default = NULL)
	{
		if (!$this->hasParameter($name)) {
			if (func_num_args() < 2) {
				throw new InvalidStateException(sprintf('No parameter "%s" found', $name));
			}

			return $default;
		}

		return $this->getAttribute(self::ATTR_PARAMETERS)[$name];
	}

	/**
	 * @return mixed[]
	 */
	public function getParameters()
	{
		return $this->getAttribute(self::ATTR_PARAMETERS);
	}

}
