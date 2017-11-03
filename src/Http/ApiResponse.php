<?php

namespace Apitte\Core\Http;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Schema\Endpoint;
use Apitte\Negotiation\Http\AbstractEntity;
use Contributte\Psr7\ProxyResponse;

/**
 * Tiny wrapper for PSR-7 ResponseInterface
 */
class ApiResponse extends ProxyResponse
{

	/** @var array */
	protected $attributes = [];

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getAttribute($name, $default = NULL)
	{
		if (!$this->hasAttribute($name)) {
			if (func_num_args() < 2) {
				throw new InvalidStateException(sprintf('No attribute "%s" found', $name));
			}

			return $default;
		}

		return $this->attributes[$name];
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return static
	 */
	public function withAttribute($name, $value)
	{
		$new = clone $this;
		$new->attributes[$name] = $value;

		return $new;
	}


	/**
	 * @return AbstractEntity
	 */
	public function getEntity()
	{
		return $this->getAttribute(ResponseAttributes::ATTR_ENTITY, NULL);
	}

	/**
	 * @param AbstractEntity $entity
	 * @return static
	 */
	public function withEntity(AbstractEntity $entity)
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENTITY, $entity);
	}

	/**
	 * @return Endpoint
	 */
	public function getEndpoint()
	{
		return $this->getAttribute(ResponseAttributes::ATTR_ENDPOINT, NULL);
	}

	/**
	 * @param Endpoint $endpoint
	 * @return static
	 */
	public function withEndpoint(Endpoint $endpoint)
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENDPOINT, $endpoint);
	}

}
