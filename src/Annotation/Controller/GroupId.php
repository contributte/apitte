<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class GroupId
{

	/** @var string */
	private $name;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value'])) {
			if (empty($values['value'])) {
				throw new AnnotationException('Empty @GroupId given');
			}
			$this->name = $values['value'];
		} elseif (isset($values['name'])) {
			if (empty($values['name'])) {
				throw new AnnotationException('Empty @GroupId given');
			}
			$this->name = $values['name'];
		} else {
			throw new AnnotationException('No @GroupId given');
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

}
