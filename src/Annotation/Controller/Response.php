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
	private $code = 'default';

	/** @var string */
	private $description;

	/** @var string|null */
	private $entity;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['description']) || empty($values['description'])) {
			throw new AnnotationException('Empty @Response description given');
		}
		$this->code = $values['code'] ?? 'default';
		$this->entity = $values['entity'] ?? null;
		$this->description = $values['description'];
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
