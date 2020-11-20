<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("ANNOTATION")
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

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		if (!array_key_exists('name', $values)) {
			throw new AnnotationException('No @RequestParameter name given');
		}

		$name = $values['name'];
		if ($name === null || $name === '') {
			throw new AnnotationException('Empty @RequestParameter name given');
		}

		if (!array_key_exists('type', $values)) {
			throw new AnnotationException('No @RequestParameter type given');
		}

		$type = $values['type'];
		if ($type === null || $type === '') {
			throw new AnnotationException('Empty @RequestParameter type given');
		}

		$this->name = $name;
		$this->type = $type;
		$this->required = $values['required'] ?? true;
		$this->allowEmpty = $values['allowEmpty'] ?? false;
		$this->deprecated = $values['deprecated'] ?? false;
		$this->description = $values['description'] ?? null;
		$this->in = $values['in'] ?? EndpointParameter::IN_PATH;
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
