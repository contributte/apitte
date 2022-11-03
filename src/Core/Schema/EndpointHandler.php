<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointHandler
{

	/** @var class-string */
	private string $class;

	private string $method;

	/**
	 * @param class-string $class
	 */
	public function __construct(string $class, string $method)
	{
		$this->class = $class;
		$this->method = $method;
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
