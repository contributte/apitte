<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Negotiations
{

	/** @var Negotiation[] */
	private $negotations = [];

	/**
	 * @param Negotiation[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @Negotiations given');
			}
			$this->negotations = $values['value'];
		} else if (isset($values['negotations'])) {
			if (empty($values['negotations'])) {
				throw new AnnotationException('Empty @Negotiations given');
			}
			$this->negotations = $values['negotations'];
		} else {
			throw new AnnotationException('No @Negotiationss given');
		}
	}

	/**
	 * @return Negotiation[]
	 */
	public function getNegotations()
	{
		return $this->negotations;
	}

}
