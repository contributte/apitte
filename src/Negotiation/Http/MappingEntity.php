<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

use Apitte\Core\Mapping\Response\IResponseEntity;

class MappingEntity extends AbstractEntity
{

	public function __construct(
		protected IResponseEntity $entity,
	)
	{
		parent::__construct();
	}

	/**
	 * @return static
	 */
	public static function from(IResponseEntity $entity): self
	{
		return new static($entity);
	}

	/**
	 * @return mixed[]
	 */
	public function getData(): array
	{
		return $this->entity->toResponse();
	}

	public function getEntity(): IResponseEntity
	{
		return $this->entity;
	}

}
