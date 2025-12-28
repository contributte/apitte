<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Path
{

	public function __construct(
		private readonly string $path,
	)
	{
		if ($path === '') {
			throw new InvalidArgumentException('Empty #[Path] given');
		}
	}

	public function getPath(): string
	{
		return $this->path;
	}

}
