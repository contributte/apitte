<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class IntegerTypeMapper implements ITypeMapper
{

	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $value, array $options = []): int
	{
		if (is_int($value) || (is_string($value) && preg_match('#^[+-]?[0-9]+\z#', $value) === 1)) {
			return (int) $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_INTEGER);
	}

}
