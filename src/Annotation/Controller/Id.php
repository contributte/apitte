<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Id
{

	/** @var string */
	private $name;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['value'])) {
			throw new AnnotationException('No @Id given');
		}

		$value = $values['value'];
		if ($value === null || $value === '') {
			throw new AnnotationException('Empty @Id given');
		}

		$this->name = $value;
	}

	public function getName(): string
	{
		return $this->name;
	}

}
