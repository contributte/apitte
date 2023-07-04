<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Tag
{

	private string $name;

	private ?string $value;

	public function __construct(string $name, ?string $value = null)
	{
		if ($name === '') {
			throw new AnnotationException('Empty @Tag name given');
		}

		$this->name = $name;
		$this->value = $value;
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
