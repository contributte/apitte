<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Mapping\Response\IResponseEntity;
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
		if (!isset($values['entity']) || empty($values['entity'])) {
			throw new AnnotationException('Empty @ResponseMapper entity given');
		}

		if (!class_exists($values['entity'])) {
			throw new AnnotationException(sprintf('@ResponseMapper entity "%s" does not exists', $values['entity']));
		}

		if (!isset(class_implements($values['entity'])[IResponseEntity::class])) {
			throw new AnnotationException(sprintf('@ResponseMapper entity "%s" does not implements "%s"', $values['entity'], IResponseEntity::class));
		}

		$this->entity = $values['entity'];
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

}
