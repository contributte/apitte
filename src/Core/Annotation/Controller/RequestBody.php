<?php declare(strict_types = 1);

namespace Apitte\Core\Annotation\Controller;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD)]
class RequestBody
{

	public function __construct(
		private readonly ?string $description = null,
		private readonly ?string $entity = null,
		private readonly bool $required = false,
		private readonly bool $validation = true,
	)
	{
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
