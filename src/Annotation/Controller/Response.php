<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Response
{

	/** @var string */
	private $code;

	/** @var string */
	private $description;

	/** @var string|null */
	private $entity;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!array_key_exists('description', $values)) {
			throw new AnnotationException('No @Response description given');
		}

		$description = $values['description'];
		if ($description === null || $description === '') {
			throw new AnnotationException('Empty @Response description given');
		}

		$this->code = $values['code'] ?? 'default';
		$this->entity = $values['entity'] ?? null;
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
