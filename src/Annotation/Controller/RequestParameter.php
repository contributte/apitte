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

	/** @var string|null */
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
		if (!isset($values['name']) || empty($values['name'])) {
			throw new AnnotationException('Empty @RequestParameter name given');
		}

		if ((!isset($values['type']) || empty($values['type'])) &&
			(!isset($values['description']) || empty($values['description']))
		) {
			throw new AnnotationException('Non-empty type or description is required at @RequestParameter');
		}

		$this->name = $values['name'];
		$this->description = $values['description'] ?? null;
		$this->required = $values['required'] ?? true;
		$this->allowEmpty = $values['allowEmpty'] ?? false;
		$this->deprecated = $values['deprecated'] ?? false;

		$this->type = $values['type'] ?? null;
		if (!in_array($this->type, array_merge(EndpointParameter::TYPES, [null]), true)) {
			throw new AnnotationException(sprintf('Invalid @RequestParameter type "%s". Choose one of %s::TYPE_*', $this->type, EndpointParameter::class));
		}

		$this->in = $values['in'] ?? EndpointParameter::IN_PATH;
		if (!in_array($this->in, EndpointParameter::IN, true)) {
			throw new AnnotationException(sprintf('Invalid @RequestParameter in "%s". Choose one of %s::IN_*', $this->in, EndpointParameter::class));
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): ?string
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
