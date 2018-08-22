<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

class RequestMapper
{

	/** @var string */
	private $entity;

	/** @var bool */
	private $validation;

	public function __construct(string $entity, bool $validation = true)
	{
		$this->entity = $entity;
		$this->validation = $validation;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

	public function isValidation(): bool
	{
		return $this->validation;
	}

}
