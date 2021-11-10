<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

class ScalarEntity extends AbstractEntity
{

	/**
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		parent::__construct($value);
	}

	/**
	 * @param mixed $value
	 * @return static
	 */
	public static function from($value): self
	{
		return new static($value);
	}

}
