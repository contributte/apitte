<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Id
{

	public function __construct(
		private readonly string $name,
	)
	{
		if ($name === '') {
			throw new InvalidArgumentException('Empty #[Id] given');
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

}
