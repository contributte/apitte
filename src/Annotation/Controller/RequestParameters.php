<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RequestParameters
{

	/** @var RequestParameter[] */
	private $parameters = [];

	/**
	 * @param RequestParameter[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @RequestParameters given');
			}
			$this->parameters = $values['value'];
		} else if (isset($values['parameters'])) {
			if (empty($values['parameters'])) {
				throw new AnnotationException('Empty @RequestParameters given');
			}
			$this->parameters = $values['parameters'];
		} else {
			throw new AnnotationException('No @RequestParameters given');
		}
	}

	/**
	 * @return RequestParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

}
