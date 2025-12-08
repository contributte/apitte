<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

class EndpointHandler
{

	/**
	 * @param class-string $class
	 */
	public function __construct(
		private readonly string $class,
		private readonly string $method,
	)
	{
	}

	/**
	 * @return class-string
	 */
	public function getClass(): string
	{
		return $this->class;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

}
