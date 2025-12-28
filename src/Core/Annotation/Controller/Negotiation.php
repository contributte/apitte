<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Negotiation
{

	public function __construct(
		private readonly string $suffix,
		private readonly bool $default = false,
		private readonly ?string $renderer = null,
	)
	{
	}

	public function getSuffix(): string
	{
		return $this->suffix;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function getRenderer(): ?string
	{
		return $this->renderer;
	}

}
