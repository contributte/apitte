<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class Tag
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $value;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (isset($values['value']) && !isset($values['name'])) {
			$values['name'] = $values['value'];
		}

		if (!array_key_exists('name', $values)) {
			throw new AnnotationException('No @Tag name given');
		}

		$name = $values['name'];
		if ($name === null || $name === '') {
			throw new AnnotationException('Empty @Tag name given');
		}

		$this->name = $name;
		$this->value = $values['value'] ?? null;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

}
