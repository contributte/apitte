<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @NamedArgumentConstructor()
 */
final class Id
{

	/** @var string */
	private $name;

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
