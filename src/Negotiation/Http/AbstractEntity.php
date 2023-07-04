<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

abstract class AbstractEntity
{

	protected mixed $data;

	public function __construct(mixed $data = null)
	{
		$this->data = $data;
	}

	public function getData(): mixed
	{
		return $this->data;
	}

	protected function setData(mixed $data): void
	{
		$this->data = $data;
	}

}
