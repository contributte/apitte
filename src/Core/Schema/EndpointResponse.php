<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

class EndpointResponse
{

	private ?string $entity = null;

	public function __construct(
		private readonly string $code,
		private readonly string $description,
	)
	{
	}

	public function setEntity(?string $entity): void
	{
		$this->entity = $entity;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

}
