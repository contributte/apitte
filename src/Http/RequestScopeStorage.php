<?php declare(strict_types = 1);

namespace Apitte\Core\Http;

class RequestScopeStorage
{

	/** @var mixed[] */
	private $data = [];

	/**
	 * @param mixed $data
	 */
	public function save(string $key, $data): void
	{
		$this->data[$key] = $data;
	}

	public function has(string $key): bool
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function load(string $key, $default = null)
	{
		if ($this->has($key)) {
			return $this->data[$key];
		}

		return $default;
	}

	/**
	 * @internal
	 */
	public function clear(): void
	{
		$this->data = [];
	}

}
