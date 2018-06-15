<?php declare(strict_types = 1);

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

	/** @var mixed[] */
	protected $attributes = [];

	public function hasAttribute(string $name): bool
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getAttribute(string $name, $default = null)
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
	 * @return mixed[]
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @param mixed $value
	 */
	public function withAttribute(string $name, $value): self
	{
		$new = clone $this;
		$new->attributes[$name] = $value;

		return $new;
	}

	public function getEntity(): ?AbstractEntity
	{
		return $this->getAttribute(ResponseAttributes::ATTR_ENTITY, null);
	}

	public function withEntity(AbstractEntity $entity): self
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENTITY, $entity);
	}

	public function getEndpoint(): Endpoint
	{
		return $this->getAttribute(ResponseAttributes::ATTR_ENDPOINT, null);
	}

	public function withEndpoint(Endpoint $endpoint): self
	{
		return $this->withAttribute(ResponseAttributes::ATTR_ENDPOINT, $endpoint);
	}

}
