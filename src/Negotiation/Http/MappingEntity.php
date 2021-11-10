<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

use Apitte\Core\Mapping\Response\IResponseEntity;

class MappingEntity extends AbstractEntity
{

	/** @var IResponseEntity */
	protected $entity;

	public function __construct(IResponseEntity $entity)
	{
		parent::__construct();
		$this->entity = $entity;
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

	/**
	 * @return static
	 */
	public static function from(IResponseEntity $entity): self
	{
		return new static($entity);
	}

}
