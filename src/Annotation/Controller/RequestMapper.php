<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Mapping\Request\IRequestEntity;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequestMapper
{

	/** @var string */
	private $entity;

	/** @var bool */
	private $validation = true;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['entity']) || empty($values['entity'])) {
			throw new AnnotationException('Empty @RequestMapper entity given');
		}

		if (!class_exists($values['entity'])) {
			throw new AnnotationException(sprintf('@RequestMapper entity "%s" does not exists', $values['entity']));
		}

		if (!isset(class_implements($values['entity'])[IRequestEntity::class])) {
			throw new AnnotationException(sprintf('@RequestMapper entity "%s" does not implements "%s"', $values['entity'], IRequestEntity::class));
		}

		$this->entity = $values['entity'];

		if (isset($values['validation'])) {
			$this->validation = $values['validation'];
		}
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

	public function isValidation(): bool
	{
		return $this->validation;
	}

}
