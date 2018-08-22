<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointHandler
{

	/** @var string */
	private $class;

	/** @var string */
	private $method;

	/** @var mixed[] */
	private $arguments = [];

	public function __construct(string $class, string $method)
	{
		$this->class = $class;
		$this->method = $method;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @return mixed[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function setArguments(array $arguments): void
	{
		$this->arguments = $arguments;
	}

}
