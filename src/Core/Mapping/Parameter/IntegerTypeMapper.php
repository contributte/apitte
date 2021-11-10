<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class IntegerTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): int
	{
		if (is_int($value) || (is_string($value) && preg_match('#^[+-]?[0-9]+\z#', $value))) {
			return (int) $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_INTEGER);
	}

}
