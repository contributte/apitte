<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

final class EndpointParameter
{

	public const TYPE_SCALAR = 'scalar';
	public const TYPE_STRING = 'string';
	public const TYPE_INTEGER = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOLEAN = 'bool';
	public const TYPE_DATETIME = 'datetime';
	public const TYPE_OBJECT = 'object';

	public const TYPES = [
		self::TYPE_SCALAR,
		self::TYPE_STRING,
		self::TYPE_INTEGER,
		self::TYPE_FLOAT,
		self::TYPE_BOOLEAN,
		self::TYPE_DATETIME,
		self::TYPE_OBJECT,
	];

	public const IN_QUERY = 'query';
	public const IN_COOKIE = 'cookie';
	public const IN_HEADER = 'header';
	public const IN_PATH = 'path';

	public const IN = [
		self::IN_QUERY,
		self::IN_COOKIE,
		self::IN_HEADER,
		self::IN_PATH,
	];

	/** @var string|null */
	private $name;

	/** @var string */
	private $type = self::TYPE_SCALAR;

	/** @var string|null */
	private $description;

	/** @var string */
	private $in = self::IN_PATH;

	/** @var bool */
	private $required = true;

	/** @var bool */
	private $deprecated = false;

	/** @var bool */
	private $allowEmpty = false;

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
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
