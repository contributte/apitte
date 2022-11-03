<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointResponse
{

	private string $code;

	private string $description;

	private ?string $entity = null;

	public function __construct(string $code, string $description)
	{
		$this->code = $code;
		$this->description = $description;
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
