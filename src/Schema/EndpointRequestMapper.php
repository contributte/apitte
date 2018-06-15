<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointRequestMapper
{

	/** @var string|null */
	private $entity;

	/** @var bool */
	private $validation = true;

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function setEntity(?string $entity): void
	{
		$this->entity = $entity;
	}

	public function isValidation(): bool
	{
		return $this->validation;
	}

	public function setValidation(bool $validation): void
	{
		$this->validation = $validation;
	}

}
