<?php

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Method
{

	/** @var array */
	private $methods = [];

	/**
	 * @param array $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (is_array($values['value'])) {
				$this->methods = $values['value'];
			} else if (is_string($values['value']) && !empty($values['value'])) {
				$this->methods = [$values['value']];
			} else {
				throw new AnnotationException('Invalid @Method given');
			}
		} else if (isset($values['methods']) && !empty($values['methods'])) {
			$this->methods = $values['methods'];
		} else if (isset($values['method'])) {
			$this->methods = [$values['method']];
		} else {
			throw new AnnotationException('No @Method given');
		}
	}

	/**
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}

}
