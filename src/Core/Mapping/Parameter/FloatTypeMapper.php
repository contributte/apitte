<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class FloatTypeMapper implements ITypeMapper
{

	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $value, array $options = []): ?float
	{
		if (is_string($value)) {
			$value = str_replace(',', '.', $value); // Accept also comma as decimal separator
		}

		if (is_float($value) || is_int($value) || (is_string($value) && preg_match('#^[+-]?[0-9]*[.]?[0-9]+\z#', $value) === 1)) {
			return (float) $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_FLOAT);
	}

}
