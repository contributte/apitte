<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class Id
{

	/** @var string */
	private $name;

	/**
	 * @param array<string, mixed|null> $values
	 */
	public function __construct(array $values)
	{
		if (!array_key_exists('value', $values)) {
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
