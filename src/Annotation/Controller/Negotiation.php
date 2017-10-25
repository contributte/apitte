<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
abstract class Negotiation
{

	/** @var string */
	private $type;

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['type']) && !isset($values['description'])) {
			throw new AnnotationException('Type is required at @AbstractNegotiation');
		}

		$this->type = $values['type'];
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

}
