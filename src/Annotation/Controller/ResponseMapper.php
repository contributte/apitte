<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class ResponseMapper
{

	/** @var string */
	private $entity;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['entity'])) {
			throw new AnnotationException('No @ResponseMapper entity given');
		}

		$entity = $values['entity'];
		if ($entity === null || $entity === '') {
			throw new AnnotationException('Empty @ResponseMapper entity given');
		}

		$this->entity = $entity;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

}
