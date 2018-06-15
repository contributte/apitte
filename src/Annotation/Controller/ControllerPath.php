<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ControllerPath
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
				throw new AnnotationException('Empty @ControllerPath given');
			}
			$this->path = $values['value'];
		} elseif (isset($values['path'])) {
			if (empty($values['path'])) {
				throw new AnnotationException('Empty @ControllerPath given');
			}
			$this->path = $values['path'];
		} else {
			throw new AnnotationException('No @ControllerPath given');
		}
	}

	public function getPath(): string
	{
		return $this->path;
	}

}
