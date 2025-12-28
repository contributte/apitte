<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Tag
{

	public function __construct(
		private readonly string $name,
		private readonly ?string $value = null,
	)
	{
		if ($name === '') {
			throw new InvalidArgumentException('Empty #[Tag] name given');
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

}
