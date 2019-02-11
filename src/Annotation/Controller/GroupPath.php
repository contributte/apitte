<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class GroupPath
{

	/** @var string */
	private $path;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['value'])) {
			throw new AnnotationException('No @GroupPath given');
		}

		$value = $values['value'];
		if ($value === null || $value === '') {
			throw new AnnotationException('Empty @GroupPath given');
		}

		$this->path = $value;
	}

	public function getPath(): string
	{
		return $this->path;
	}

}
