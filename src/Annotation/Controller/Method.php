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
		if (!isset($values['value'])) {
			throw new AnnotationException('No @Method given');
		}

		$methods = $values['value'];
		if ($methods === [] || $methods === null || $methods === '') {
			throw new AnnotationException('Empty @Method given');
		}

		// Wrap single given method into array
		if (!is_array($methods)) {
			$methods = [$methods];
		}

		$this->methods = $methods;
	}

	/**
	 * @return string[]
	 */
	public function getMethods(): array
	{
		return $this->methods;
	}

}
