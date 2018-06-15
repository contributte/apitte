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
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @GroupPath given');
			}
			$this->path = $values['value'];
		} elseif (isset($values['path'])) {
			if (empty($values['path'])) {
				throw new AnnotationException('Empty @GroupPath given');
			}
			$this->path = $values['path'];
		} else {
			throw new AnnotationException('No @GroupPath given');
		}
	}

	public function getPath(): string
	{
		return $this->path;
	}

}
