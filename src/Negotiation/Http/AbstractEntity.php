<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

abstract class AbstractEntity
{

	public function __construct(
		protected mixed $data = null,
	)
	{
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
