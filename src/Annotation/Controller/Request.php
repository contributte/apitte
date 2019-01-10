<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Request
{

	/** @var string|null */
	private $description;

	/** @var string|null */
	private $entity;

	/** @var bool */
	private $required = false;

	/**
	 * @param mixed[] $values
	 */
	public function __construct(array $values)
	{
		$this->description = $values['description'] ?? null;
		$this->description = $values['entity'] ?? null;
		if (isset($values['required']) && $values['required'] === true) {
			$this->required = true;
		}
	}

	public function getEntity(): ?string
	{
		return $this->entity;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

}
