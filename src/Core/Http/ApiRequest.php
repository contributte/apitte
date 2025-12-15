<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Contributte\Psr7\Extra\ExtraRequestTrait;
use Contributte\Psr7\ProxyRequest;

class ApiRequest extends ProxyRequest
{

	use ExtraRequestTrait;

	public function hasParameter(string $name): bool
	{
		/** @var array<string, mixed> $params */
		$params = $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, []);

		return array_key_exists($name, $params);
	}

	public function getParameter(string $name, mixed $default = null): mixed
	{
		/** @var array<string, mixed> $params */
		$params = $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, []);

		return $params[$name] ?? $default;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getParameters(): array
	{
		/** @var array<string, mixed> $params */
		$params = $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, []);

		return $params;
	}

	public function getEntity(mixed $default = null): mixed
	{
		$entity = $this->getAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, null);

		if ($entity === null) {
			if (func_num_args() < 1) {
				throw new InvalidStateException('No request entity found');
			}

			return $default;
		}

		return $entity;
	}

}
