<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Negotiation
{

	/** @var string */
	private $suffix;

	/** @var bool */
	private $default;

	/** @var string|null */
	private $renderer;

	public function __construct(string $suffix, bool $default = false, ?string $renderer = null)
	{
		$this->suffix = $suffix;
		$this->default = $default;
		$this->renderer = $renderer;
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
