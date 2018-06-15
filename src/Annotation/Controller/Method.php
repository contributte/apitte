<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Method
{

	/** @var string[] */
	private $methods = [];

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (is_array($values['value'])) {
				$this->methods = $values['value'];
			} elseif (is_string($values['value']) && !empty($values['value'])) {
				$this->methods = [$values['value']];
			} else {
				throw new AnnotationException('Invalid @Method given');
			}
		} elseif (isset($values['methods']) && !empty($values['methods'])) {
			$this->methods = $values['methods'];
		} elseif (isset($values['method'])) {
			$this->methods = [$values['method']];
		} else {
			throw new AnnotationException('No @Method given');
		}
	}

	/**
	 * @return string[]
	 */
	public function getMethods(): array
	{
		return $this->methods;
	}

}
