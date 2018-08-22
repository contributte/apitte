<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Negotiation
{

	/** @var string */
	private $suffix;

	/** @var bool */
	private $default;

	/** @var string|null */
	private $renderer;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!isset($values['suffix'])) {
			throw new AnnotationException('Suffix is required at @Negotiation');
		}

		$this->suffix = $values['suffix'];
		$this->default = $values['default'] ?? false;
		$this->renderer = $values['renderer'] ?? null;
	}

	public function getSuffix(): string
	{
		return $this->suffix;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function getRenderer(): ?string
	{
		return $this->renderer;
	}

}
