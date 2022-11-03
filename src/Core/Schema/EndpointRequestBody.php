<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointRequestBody
{

	private ?string $description = null;

	private ?string $entity = null;

	private bool $required = false;

	private bool $validation = true;

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function setEntity(?string $entity): void
	{
		$this->entity = $entity;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function setRequired(bool $required): void
	{
		$this->required = $required;
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
