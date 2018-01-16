<?php

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
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['entity'])) {
			throw new AnnotationException('Empty @ResponseMapper entity given');
		}

		$this->entity = $values['entity'];
	}

	/**
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}

}
