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
final class Path
{

	/** @var string */
	private $path;

	public function __construct(string $path)
	{
		if ($path === '') {
			throw new AnnotationException('Empty @Path given');
		}

		$this->path = $path;
	}

	public function getPath(): string
	{
		return $this->path;
	}

}
