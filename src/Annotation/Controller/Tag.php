<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @NamedArgumentConstructor()
 */
final class Tag
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $value;

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
