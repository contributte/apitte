<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Method
{

	/** @var string[] */
	private array $methods = [];

	/**
	 * @param string[]|string $methods
	 */
	public function __construct(array|string $methods)
	{
		if (empty($methods)) {
			throw new AnnotationException('Empty @Method given');
		}

		// Wrap single given method into array
		if (! is_array($methods)) {
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
