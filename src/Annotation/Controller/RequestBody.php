<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
final class RequestBody
{

	/** @var string|null */
	private $description;

	/** @var string|null */
	private $entity;

	/** @var bool */
	private $required;

	/** @var bool */
	private $validation;

	public function __construct(?string $description = null, ?string $entity = null, bool $required = false, bool $validation = true)
	{
		$this->description = $description;
		$this->entity = $entity;
		$this->required = $required;
		$this->validation = $validation;
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

	public function isValidation(): bool
	{
		return $this->validation;
	}

}
