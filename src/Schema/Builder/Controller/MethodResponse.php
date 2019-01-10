<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

final class MethodResponse
{

	/** @var string */
	private $code = 'default';

	/** @var string */
	private $description;

	/** @var string|null */
	private $entity;

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
