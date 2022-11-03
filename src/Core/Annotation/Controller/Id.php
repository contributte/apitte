<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Id
{

	private string $name;

	public function __construct(string $name)
	{
		if ($name === '') {
			throw new AnnotationException('Empty @Id given');
		}

		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

}
