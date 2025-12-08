<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;

class InvalidArgumentTypeException extends RuntimeException
{

	public const TYPE_INTEGER = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOLEAN = 'bool';
	public const TYPE_DATETIME = 'datetime';
	public const TYPE_ENUM = 'enum';

	public function __construct(
		private readonly string $type,
		private readonly ?string $description = null,
	)
	{
		parent::__construct();
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

}
