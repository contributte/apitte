<?php declare(strict_types = 1);

namespace Apitte\Core\Exception\Runtime;

use Apitte\Core\Exception\RuntimeException;

class InvalidArgumentTypeException extends RuntimeException
{

	public const TYPE_INTEGER = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOLEAN = 'bool';
	public const TYPE_DATETIME = 'datetime';

	/** @var string */
	private $type;

	public function __construct(string $type)
	{
		parent::__construct();
		$this->type = $type;
	}

	public function getType(): string
	{
		return $this->type;
	}

}
