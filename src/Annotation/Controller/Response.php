<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Response
{

	/** @var string */
	private $code;

	/** @var string */
	private $description;

	/** @var string|null */
	private $entity;

	public function __construct(string $description, string $code = 'default', ?string $entity = null)
	{
		if (empty($description)) {
			throw new AnnotationException('Empty @Response description given');
		}

		$this->code = $code;
		$this->entity = $entity;
		$this->description = $description;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

}
