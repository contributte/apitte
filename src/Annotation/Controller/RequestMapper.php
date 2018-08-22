<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

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
	private $validation;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['entity']) || empty($values['entity'])) {
			throw new AnnotationException('Empty @RequestMapper entity given');
		}

		$this->entity = $values['entity'];
		$this->validation = $values['validation'] ?? true;
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
