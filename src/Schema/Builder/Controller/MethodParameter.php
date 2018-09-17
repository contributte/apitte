<?php declare(strict_types = 1);

namespace Apitte\Core\Schema\Builder\Controller;

use Apitte\Core\Schema\EndpointParameter;

final class MethodParameter
{

	/** @var string */
	private $name;

	/** @var string */
	private $type;

	/** @var string|null */
	private $description;

	/** @var string */
	private $in = EndpointParameter::IN_PATH;

	/** @var bool */
	private $required = true;

	/** @var bool */
	private $deprecated = false;

	/** @var bool */
	private $allowEmpty = false;

	public function __construct(string $name, string $type = EndpointParameter::TYPE_STRING)
	{
		$this->name = $name;
		$this->type = $type;
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

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getIn(): string
	{
		return $this->in;
	}

	public function setIn(string $in): void
	{
		$this->in = $in;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	public function setRequired(bool $required): void
	{
		$this->required = $required;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	public function setDeprecated(bool $deprecated): void
	{
		$this->deprecated = $deprecated;
	}

	public function isAllowEmpty(): bool
	{
		return $this->allowEmpty;
	}

	public function setAllowEmpty(bool $allowEmpty): void
	{
		$this->allowEmpty = $allowEmpty;
	}

}
