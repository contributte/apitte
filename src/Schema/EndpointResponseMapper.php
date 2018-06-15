<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointResponseMapper
{

	/** @var string|null */
	private $entity;

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function setEntity(?string $entity): void
	{
		$this->entity = $entity;
	}

}
