<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Mapping\Request\IRequestEntity;
use Contributte\Psr7\Extra\ExtraRequestTrait;
use Contributte\Psr7\ProxyRequest;

class ApiRequest extends ProxyRequest
{

	use ExtraRequestTrait;

	public function hasParameter(string $name): bool
	{
		return array_key_exists($name, $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, []));
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getParameter(string $name, $default = null)
	{
		return $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, [])[$name] ?? $default;
	}

	/**
	 * @return mixed
	 */
	public function getParameters()
	{
		return $this->getAttribute(RequestAttributes::ATTR_PARAMETERS, []);
	}

	/**
	 * @param mixed $default
	 * @return IRequestEntity|mixed
	 */
	public function getEntity($default = null)
	{
		$entity = $this->getAttribute(RequestAttributes::ATTR_REQUEST_ENTITY, null);

		if ($entity !== null) {
			if (func_num_args() < 1) {
				throw new InvalidStateException('No request entity found');
			}

			return $default;
		}

		return $entity;
	}

}
