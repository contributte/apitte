<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Response
{

	public function __construct(
		private readonly string $description,
		private readonly string $code = 'default',
		private readonly ?string $entity = null,
	)
	{
		if (empty($description)) {
			throw new InvalidArgumentException('Empty #[Response] description given');
		}
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
