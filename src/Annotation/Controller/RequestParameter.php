<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
 * @NamedArgumentConstructor()
 */
final class RequestParameter
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var string|null */
	private $description;

	/** @var string */
	private $in;

	/** @var bool */
	private $required;

	/** @var bool */
	private $deprecated;

	/** @var bool */
	private $allowEmpty;

	public function __construct(
		string $name,
		string $type,
		string $in,
		bool $required = true,
		bool $allowEmpty = false,
		bool $deprecated = false,
		?string $description = null
	)
	{
		if ($name === '') {
			throw new AnnotationException('Empty @RequestParameter name given');
		}

		if ($type === '') {
			throw new AnnotationException('Empty @RequestParameter type given');
		}

		$this->name = $name;
		$this->type = $type;
		$this->required = $required;
		$this->allowEmpty = $allowEmpty;
		$this->deprecated = $deprecated;
		$this->description = $description;
		$this->in = $in;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getIn(): string
	{
		return $this->in;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	public function isAllowEmpty(): bool
	{
		return $this->allowEmpty;
	}

}
