<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class OpenApi
{

	private readonly string $data;

	public function __construct(string $data)
	{
		$this->data = $this->purifyDocblock($data);
	}

	public function getData(): string
	{
		return $this->data;
	}

	private function purifyDocblock(string $docblock): string
	{
		// Removes useless whitespace and * from start of every line
		return preg_replace('#\s*\*\/$|^\s*\*\s{0,1}|^\/\*{1,2}#m', '', $docblock);
	}

}
