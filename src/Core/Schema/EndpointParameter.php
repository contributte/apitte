<?php declare(strict_types = 1);

namespace Apitte\Core\Schema;

class EndpointParameter
{

	public const TYPE_STRING = 'string';
	public const TYPE_INTEGER = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOLEAN = 'bool';
	public const TYPE_DATETIME = 'datetime';
	public const TYPE_ENUM = 'enum';

	public const TYPES = [
		self::TYPE_STRING,
		self::TYPE_INTEGER,
		self::TYPE_FLOAT,
		self::TYPE_BOOLEAN,
		self::TYPE_DATETIME,
		self::TYPE_ENUM,
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

	private ?string $description = null;

	private string $in = self::IN_PATH;

	private bool $required = true;

	private bool $deprecated = false;

	private bool $allowEmpty = false;

	/** @var list<string|int>|null */
	private ?array $enum = null;

	public function __construct(
		private readonly string $name,
		private readonly string $type = self::TYPE_STRING,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getSchemaType(): string
	{
		return match ($this->type) {
			self::TYPE_STRING, self::TYPE_FLOAT, self::TYPE_DATETIME => $this->type,
			self::TYPE_BOOLEAN => 'boolean',
			self::TYPE_INTEGER => 'integer',
			self::TYPE_ENUM => 'string',
			default => 'string',
		};
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

	/**
	 * @return list<string|int>|null
	 */
	public function getEnum(): ?array
	{
		return $this->enum;
	}

	/**
	 * @param list<string|int>|null $enum
	 */
	public function setEnum(?array $enum): void
	{
		$this->enum = $enum;
	}

}
