<?php declare(strict_types = 1);

namespace Apitte\Negotiation\Http;

class ScalarEntity extends AbstractEntity
{

	public function __construct(mixed $value)
	{
		parent::__construct($value);
	}

	/**
	 * @return static
	 */
	public static function from(mixed $value): self
	{
		return new static($value);
	}

}
