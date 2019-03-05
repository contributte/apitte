<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;

class FloatTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?float
	{
		if (is_string($value)) {
			$value = str_replace(',', '.', $value); // Accept also comma as decimal separator
		}

		if (is_float($value) || is_int($value) || (is_string($value) && preg_match('#^-?[0-9]*[.]?[0-9]+\z#', $value))) {
			return (float) $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_FLOAT);
	}

	/**
	 * @param mixed $value
	 */
	public function denormalize($value): string
	{
		return (string) $value;
	}

}
