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
		if (!isset($values['entity']) || empty($values['entity'])) {
			throw new AnnotationException('Empty @ResponseMapper entity given');
		}

		$this->entity = $values['entity'];
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

}
