<?php declare(strict_types = 1);

namespace Apitte\Core\Mapping\Parameter;

use Apitte\Core\Exception\Runtime\InvalidArgumentTypeException;
use DateTimeImmutable;
use DateTimeInterface;
use TypeError;

class DateTimeTypeMapper implements ITypeMapper
{

	/**
	 * @param mixed $value
	 */
	public function normalize($value): ?DateTimeImmutable
	{
		try {
			$value = DateTimeImmutable::createFromFormat(DATE_ATOM, $value);
		} catch (TypeError $e) {
			throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_DATETIME);
		}

		if ($value !== false) {
			return $value;
		}

		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_DATETIME);
	}

	/**
	 * @param mixed $value
	 * @throws InvalidArgumentTypeException
	 */
	public function denormalize($value): string
	{
		if ($value instanceof DateTimeInterface) {
			return $value->format(DATE_ATOM);
		}

		//TODO - support different date formats?
		throw new InvalidArgumentTypeException(InvalidArgumentTypeException::TYPE_DATETIME);
	}

}
