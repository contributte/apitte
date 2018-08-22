<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

class ResponseMapper
{

	/** @var string */
	private $entity;

	public function __construct(string $entity)
	{
		$this->entity = $entity;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

}
