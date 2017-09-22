<?php

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

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @RequestMapper given');
			}
			$this->entity = $values['value'];
		} else if (isset($values['entity'])) {
			if (empty($values['entity'])) {
				throw new AnnotationException('Empty @RequestMapper given');
			}
			$this->entity = $values['entity'];
		} else {
			throw new AnnotationException('No @RequestMapper given');
		}
	}

	/**
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}

}
