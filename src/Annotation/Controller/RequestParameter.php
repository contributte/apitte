<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Apitte\Core\Schema\EndpointParameter;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;
use Nette\Utils\Arrays;

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
		if (!isset($values['name'])) {
			throw new AnnotationException('Name is required at @RequestParameter');
		}

		if (!isset($values['type']) && !isset($values['description'])) {
			throw new AnnotationException('Type or description is required at @RequestParameter');
		}

		$this->name = $values['name'];
		$this->type = Arrays::get($values, 'type', null);
		$this->description = Arrays::get($values, 'description', null);

		// @todo validation allowed values
		$this->in = Arrays::get($values, 'in', EndpointParameter::IN_PATH);
		$this->required = Arrays::get($values, 'required', true);
		$this->deprecated = Arrays::get($values, 'deprecated', false);
		$this->allowEmpty = Arrays::get($values, 'allowEmpty', false);
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
