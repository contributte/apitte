<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointHandler
{

	/** @var string|null */
	private $class;

	/** @var string|null */
	private $method;

	/** @var mixed[] */
	private $arguments = [];

	public function getClass(): ?string
	{
		return $this->class;
	}

	public function setClass(?string $class): void
	{
		$this->class = $class;
	}

	public function getMethod(): ?string
	{
		return $this->method;
	}

	public function setMethod(?string $method): void
	{
		$this->method = $method;
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
